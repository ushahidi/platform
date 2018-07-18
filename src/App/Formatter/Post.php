<?php

/**
 * Ushahidi API Formatter for Post
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Post extends API
{
    use FormatterAuthorizerMetadata;

    public function __invoke($post)
    {
        // prefer doing it here until we implement parent method for filtering results
        // mixing and matching with metadata is just plain ugly
        $data = parent::__invoke($post);

        if (!in_array('read_full', $data['allowed_privileges'])) {
            // Remove sensitive fields
            unset($data['author_realname']);
            unset($data['author_email']);
        }

        return $data;
    }

    protected function getFieldName($field)
    {
        $remap = [
            'form_id' => 'form',
            'message_id' => 'message',
            'contact_id' => 'contact'
            ];

        if (isset($remap[$field])) {
            return $remap[$field];
        }

        return parent::getFieldName($field);
    }

    protected function formatFormId($form_id)
    {
        return $this->getRelation('forms', $form_id);
    }

    protected function formatMessageId($form_id)
    {
        return $this->getRelation('messages', $form_id);
    }

    protected function formatContactId($contact_id)
    {
        return $this->getRelation('contact', $contact_id);
    }

    protected function formatColor($value)
    {
        // enforce a leading hash on color, or null if unset
        $value = ltrim($value, '#');
        return $value ? '#' . $value : null;
    }

    protected function formatTags($tags)
    {
        $output = [];
        foreach ($tags as $tagid) {
            $output[] = $this->getRelation('tags', $tagid);
        }

        return $output;
    }

    protected function formatPostDate($value)
    {
        return $value ? $value->format(\DateTime::W3C) : null;
    }
}
