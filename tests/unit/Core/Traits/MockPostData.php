<?php

namespace Tests\Unit\Core\Traits;

class MockPostData
{
    use \Ushahidi\Core\Traits\StatefulData;
    
    protected $id;
    protected $parent_id;
    protected $form_id;
    protected $user_id;
    protected $message_id;
    // Color is taken from the asscoiated form entity
    protected $color;
    protected $type;
    protected $title;
    protected $slug;
    protected $content;
    protected $author_email;
    protected $author_realname;
    protected $status;
    protected $created;
    protected $updated;
    protected $locale;
    protected $values;
    protected $post_date;
    protected $tags;
    protected $published_to;
    protected $completed_stages;
    protected $sets;
    // Source when from external provider: SMS, Email, etc
    protected $source;
    // When originating in an SMS message
    protected $contact_id;

    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
    }

    public function __isset($key)
    {
        return property_exists($this, $key);
    }

    public function asArray()
    {
        return get_object_vars($this);
    }

    protected function setStateValue($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }

    protected function getDefinition()
    {
        return [
            'id'              => 'int',
            'parent_id'       => 'int',
            'form'            => false, /* alias */
            'form_id'         => 'int',
            'user'            => false, /* alias */
            'user_id'         => 'int',
            'type'            => 'string',
            'title'           => 'string',
            'slug'            => '*slug',
            'content'         => 'string',
            'author_email'    => 'string', /* @todo email filter */
            'author_realname' => 'string', /* @todo redundent with user record */
            'status'          => 'string',
            'created'         => 'int',
            'updated'         => 'int',
            'post_date'       => '*date',
            'locale'          => '*lowercasestring',
            'values'          => 'array',
            'tags'            => 'array',
            'published_to'    => '*json',
            'completed_stages'=> 'array',
            'sets'            => 'array',
        ];
    }

    protected function getDerived()
    {
        return [
            'slug'    => function ($data) {
                if (array_key_exists('title', $data)) {
                    // Truncate the title to 137 chars so that the
                    // 13 char uniqid will fit
                    $slug = $data['title'];
                    if (strlen($slug) >= 137) {
                        $slug = substr($slug, 0, 136);
                    }
                    return $slug . ' ' . uniqid();
                }
                return false;
            },
            'form_id'   => ['form', 'form.id'], /* alias */
            'user_id'   => ['user', 'user.id'], /* alias */
            'parent_id' => ['parent', 'parent.id'], /* alias */
        ];
    }
}
