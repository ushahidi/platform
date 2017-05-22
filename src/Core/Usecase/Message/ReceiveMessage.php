<?php

/**
 * Ushahidi Platform Receive Message Use Case
 *
 * - Takes a received SMS message
 * - finds/creates the associated contact
 * - Stores the raw message
 * - Creates a new un-typed post from the message
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Usecase\CreateRepository;

use Ushahidi\Core\Exception\ValidatorException;

class ReceiveMessage extends CreateUsecase
{
	/**
	 * @var CreateRepository
	 */
	protected $postRepo;

	/**
	 * Inject a post repository
	 *
	 * @param  $repo CreateRepository
	 * @return $this
	 */
	public function setPostRepository(CreateRepository $postRepo)
	{
		$this->postRepo = $postRepo;
		return $this;
	}

	/**
	 * @var CreateRepository
	 */
	protected $contactRepo;

	/**
	 * Inject a contact repository
	 *
	 * @param  $repo CreateRepository
	 * @return $this
	 */
	public function setContactRepository(CreateRepository $contactRepo)
	{
		$this->contactRepo = $contactRepo;
		return $this;
	}

	/**
	 * @var Validator
	 */
	protected $contactValidator;

	/**
	 * Inject a contact validator
	 *
	 * @param  $repo Validator
	 * @return $this
	 */
	public function setContactValidator(Validator $contactValidator)
	{
		$this->contactValidator = $contactValidator;
		return $this;
	}

	// Usecase
	public function interact()
	{
		// Fetch and hydrate the message entity...
		$entity = $this->getEntity();

		// ... verify that the message entity can be created by the current user
		$this->verifyReceiveAuth($entity);

		// ... verify that the message entity is in a valid state
		$this->verifyValid($entity);

		// Find or create contact
		$contact = $this->getContactEntity();

		// ... verify the contact is valid
		$this->verifyValidContact($contact);

		// ... create contact for message
		$contact_id = $this->createContact($contact);
		$entity->setState(compact('contact_id'));

		// ... create post for message
		$post_id = $this->createPost($entity);
		$entity->setState(compact('post_id'));

		// ... persist the new message entity
		$id = $this->repo->create($entity);

		// ... and return message id
		return $id;
	}

	/**
	 * Get an empty entity, apply the payload.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		return $this->repo->getEntity()->setState($this->payload + [
				'status' => Message::RECEIVED,
				'direction' => Message::INCOMING
			]);
	}

	/**
	 * Create contact record for message
	 * @return Entity $contact
	 */
	protected function getContactEntity()
	{
		// Is the sender of the message a registered contact?
		$contact = $this->contactRepo->getByContact($this->getPayload('from'), $this->getPayload('contact_type'));
		if (! $contact->getId()) {
			// this is the first time a message has been received by this number, so create contact
			$contact =  $this->contactRepo->getEntity()->setState([
				'contact' => $this->getPayload('from'),
				'type' => $this->getPayload('contact_type'),
				'data_provider' => $this->getPayload('data_provider'),
			]);
		}

		return $contact;
	}

	/**
	 * Create contact (if its new)
	 * @param  Entity $contact
	 * @return Int
	 */
	protected function createContact(Entity $contact)
	{
		// If contact already existed, just return id.
		if ($contact->getId()) {
			return $contact->getId();
		}

		return $this->contactRepo->create($contact);
	}

	/**
	 * Create post for message
	 * @param  Entity $message
	 * @return Int
	 */
	protected function createPost(Entity $message)
	{
		$values = [];
		$form_id = null;

		$content = $message->message;

		if ($message->additional_data) {
			if (isset($message->additional_data['form_id'])) {
				$form_id = $message->additional_data['form_id'];
				// Check provider fields for form attribute mapping
				$inbound_fields = $message->additional_data['inbound_fields'];

				if (isset($this->payload['title']) && isset($inbound_fields['Title'])) {
						$values[$inbound_fields['Title']] = array($this->payload['title']);
				}

				if (isset($this->payload['from']) && isset($inbound_fields['From'])) {
						$values[$inbound_fields['From']] = array($this->payload['from']);
				}

				if (isset($this->payload['to']) && isset($inbound_fields['To'])) {
						$values[$inbound_fields['To']] = array($this->payload['to']);
				}

				if (isset($this->payload['message']) && isset($inbound_fields['Message'])) {
						$values[$inbound_fields['Message']] = array($this->payload['message']);
				}

				if (isset($this->payload['date']) && isset($inbound_fields['Date'])) {
						$timestamp = date("Y-m-d H:i:s", strtotime($this->payload['date']));
						$values[$inbound_fields['Date']] = array($timestamp);
				}

				if (isset($message->additional_data['location']) && isset($inbound_fields['Location'])) {
					foreach ($message->additional_data['location'] as $location) {
						if (!empty($location['type']) &&
							!empty($location['coordinates']) &&
							ucfirst($location['type']) == 'Point'
							) {
							$values[$inbound_fields['Location']][] = [
								'lon' => $location['coordinates'][0],
								'lat' => $location['coordinates'][1]
							];
						}
					}
				}
			}
			// Pull locations from extra metadata
			$values['message_location'] = [];
			if (isset($message->additional_data['location'])) {
				foreach ($message->additional_data['location'] as $location) {
					if (!empty($location['type']) &&
						!empty($location['coordinates']) &&
						ucfirst($location['type']) == 'Point'
						) {
						$values['message_location'][] = [
							'lon' => $location['coordinates'][0],
							'lat' => $location['coordinates'][1]
						];
					}
				}
			}
		}
		// First create a post
		$post = $this->postRepo->getEntity()->setState([
				'title'    => $message->title,
				'content'  => $content,
				'values'   => $values,
				'form_id'  => $form_id
			]);
		return $this->postRepo->create($post);
	}

	protected function verifyValidContact(Entity $contact)
	{
		// validate contact
		if (! $this->contactValidator->check($contact->asArray())) {
			$this->contactValidatorError($contact);
		}
	}

	/**
	 * Throw a ValidatorException
	 *
	 * @param  Entity $entity
	 * @return null
	 * @throws ValidatorException
	 */
	protected function contactValidatorError(Entity $entity)
	{
		throw new ValidatorException(sprintf(
			'Failed to validate %s entity',
			$entity->getResource()
		), $this->contactValidator->errors());
	}

	/**
	 * Verifies the current user is allowed receive access on $entity
	 *
	 * @param  Entity  $entity
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyReceiveAuth(Entity $entity)
	{
		$this->verifyAuth($entity, 'receive');
	}
}
