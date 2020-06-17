# Done

- Exclude private values ($excludePrivateValues)
```
$excludePrivateValues = !$this->postPermissions->canUserReadPrivateValues(
   $user
);
```
- Statuses
Show draft only for users with correct permission. This is done in the PostAllowed scope ;)
- Stages with Allowed scope: adds limit for hidden stages and show_when_published

# TODO 

- Exclude values that are in hidden stages (just in case, can't never be too safe I guess?!)

- Hide location via settings (anonymize location)
```
     $this->post_value_factory->getRepo('point')->hideLocation(
            !$this->postPermissions->canUserSeeLocation(
                $user,
                new Post($data),
                $this->form_repo
            )
        );
```
- Hide exact time of submission
```
 if (!$this->postPermissions->canUserSeeTime($user, new Post($data), $this->form_repo)) {
        // Hide time on survey fields
        $this->post_value_factory->getRepo('datetime')->hideTime(true);

        // @todo move to formatter. That where this normally happens
        // Replace time with 00:00:00
        if ($postDate = date_create($data['post_date'], new \DateTimeZone('UTC'))) {
            $data['post_date'] = $postDate->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        }
        if ($created = date_create('@'.$data['created'], new \DateTimeZone('UTC'))) {
            $data['created'] = $created->setTime(0, 0, 0)->format('U');
        }
        if ($updated = date_create('@'.$data['updated'], new \DateTimeZone('UTC'))) {
            $data['updated'] = $updated->setTime(0, 0, 0)->format('U');
        }
    }
```
- Hide author
```
    if (!$this->postPermissions->canUserSeeAuthor($user, new Post($data), $this->form_repo)
        && ($data['author_realname'] || $data['user_id'] || $data['author_email'])) {
        // @todo move to formatter. That where this normally happens
        unset($data['author_realname']);
        unset($data['author_email']);
        unset($data['user_id']);
    }
```

## Values
- Exclude private values (response_private)
- Exclude value if in an excluded stage (don't return excluded stages)
- Exclude attributes that should not be added (include_attributes array, check why it's used)

## Post locks
- IDK I think I have it covered but check that it's included in the output as 'lock'
```
    $data['lock'] = $this->getHydratedLock($data['id']);
```
## Sets
- Add them? 
```
    $data['sets'] = $this->getSetsForPost($data['id']);         
```

## Tags
- Verify they work fine

# Review validation rules :) some are missing 

## Categories endpoint - fix validation bug reported by Anna/Walter :) 
