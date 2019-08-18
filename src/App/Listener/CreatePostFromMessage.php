<?php
namespace Ushahidi\App\Listener;

use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Core\Entity\TargetedSurveyStateRepository;

class CreatePostFromMessage
{
    protected $messageRepo;
    protected $contactRepo;
    protected $postRepo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        MessageRepository $messageRepo,
        TargetedSurveyStateRepository $targetedSurveyStateRepo,
        PostRepository $postRepo
    ) {
        $this->targetedSurveyStateRepo = $targetedSurveyStateRepo;
        $this->messageRepo = $messageRepo;
        $this->postRepo = $postRepo;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle($id, $message, $inbound_form_id, $inbound_fields)
    {
        // \Log::info('Running CreatePostFromMessage', func_get_args());

        if ($this->targetedSurveyStateRepo->isContactInActiveTargetedSurveyAndReceivedMessage($message->contact_id)) {
            return;
        }

        $post_id = $this->createPost(
            $message,
            $inbound_form_id,
            $inbound_fields
        );

        $message->setState(compact('post_id'));

        $this->messageRepo->update($message);

        // Prevent targeted survey listener running
        return false;
    }

    /**
     * Create post for message
     *
     * @param  Entity $message
     * @return Int
     */
    protected function createPost(Message $message, $form_id, $inbound_fields)
    {
        $values = [];

        if ($form_id) {
            if (isset($message->title) && isset($inbound_fields['Title'])) {
                $values[$inbound_fields['Title']] = [$message->title];
            }

            if (isset($message->message) && isset($inbound_fields['Message'])) {
                $values[$inbound_fields['Message']] = [$message->message];
            }

            if (isset($message->datetime) && isset($inbound_fields['Date'])) {
                $timestamp = $message->datetime->format("Y-m-d H:i:s");
                $values[$inbound_fields['Date']] = [$timestamp];
            }

            if (isset($message->additional_data['location']) && isset($inbound_fields['Location'])) {
                foreach ($message->additional_data['location'] as $location) {
                    if (!empty($location['type'])
                        && !empty($location['coordinates'])
                        && ucfirst($location['type']) == 'Point'
                    ) {
                        $values[$inbound_fields['Location']][] = [
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
                'content'  => $message->message,
                'values'   => $values,
                'form_id'  => $form_id,
                'post_date'=> $message->datetime,
            ]);
        return $this->postRepo->create($post);
    }
}
