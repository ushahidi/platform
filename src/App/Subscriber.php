<?php

namespace Ushahidi\App;

use Illuminate\Contracts\Events\Dispatcher;
use Ushahidi\App\Listener\CreatePostFromMessage;
use Ushahidi\App\Listener\HandleTargetedSurveyResponse;
use Ushahidi\App\Listener\ImportPosts;
use Ushahidi\App\Listener\CreateTargetedSurveyMessageForContact;
use Ushahidi\App\Listener\IntercomAdminListener;
use Ushahidi\App\Listener\PostSetListener;

class Subscriber
{

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            'message.receive',
            HandleTargetedSurveyResponse::class
        );

        $events->listen(
            'message.receive',
            CreatePostFromMessage::class
        );

        $events->listen(
            'csv.import',
            ImportPosts::class
        );

        $events->listen(
            'form_contacts.create',
            CreateTargetedSurveyMessageForContact::class
        );

        $events->listen(
            'users.create',
            IntercomAdminListener::class
        );


        $events->listen(
            'sets.post.add',
            PostSetListener::class
        );
    }
}
