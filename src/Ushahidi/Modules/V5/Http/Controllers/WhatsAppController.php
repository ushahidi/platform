<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Ramsey\Uuid\Uuid as UUID;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Http\Resources\PostResource;
use Ushahidi\Modules\V5\Models\Message;
use Ushahidi\Modules\V5\Models\Contact;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\Common\ValidatorRunner;

/**
 * ---> WARNING: This is a crutch <---
 *
 * We are adding this specific controller for posts coming from USSD sources,
 * so that incoming posts can have contact information attached, just like SMS.
 *
 * Eventually we should have a more evolved data source framework that allows
 * bringing in structured posts with source metadata.
 */
class WhatsAppController extends PostController
{
    public function show(Request $request, int $id)
    {
        throw new \Exception("Invalid controller method");
    }

    public function index(Request $request)
    {
        throw new \Exception("Invalid controller method");
    }

    /**
     * Overriding the POST method so as to handle contact information
     */
    public function store(Request $request)
    {
        /* TODO: WhatsApp-specific authorization?
         *
         * Presently the assumption is that these come in anonymously,
         * because that's how ussd-engine operates.
         * But what would happen if they came in as an authenticated user?
         * What would be considered the source of the post? The user, or
         * the detailed shource information.
         *
         * As we are thinking of more loosely coupled datasources, this
         * should be taken into account.
         */

        /* This method works on an additional property to the post body:
         *
         *  {
         *    ...,
         *    source_info: {
         *      received: "YYYY-MM-DDTHH:MM:SSZ",
         *      data_source: "ussd",
         *      type: "phone",
         *      contact: "xxxxxxxxx"
         *    }
         *    ...
         *  }
         *
         *  Validate this property from the request.
         */
        $source_info = $request->input('source_info');
        $val_rules = [
            'source_info' => 'required',
            // TODO: couldn't get this validation just right yet
            // 'source_info.received' => 'required|string|date_format:' . \DateTime::ISO8601,
            'source_info.data_source' => 'required|string|in:whatsapp',
            'source_info.type' => 'required|string|in:phone',
            'source_info.contact' => 'required|string|min:6'
        ];
        $v = ValidatorRunner::runValidation(
            ['source_info' => $source_info],
            $val_rules,
            []  // TODO: custom messages?
        );
        if ($v->fails()) {
            return self::make422($v->getErrors());
        }

        /* Call up the parent controller to get the post created */
        $post = parent::store($request);
        if (!($post instanceof PostResource)) {
            /* An error has happened creating the post, shortcircuit to that */
            return $post;
        }

        DB::beginTransaction();
        try {
            /* Lookup / create contact if not present */
            /* assert type is phone */
            $contact = Contact::firstOrCreate([
                'data_source' => $source_info['data_source'],
                'type' => $source_info['type'],
                'contact' => $source_info['contact']
            ], [
                'can_notify' => false
            ]);

            /* Create message record */
            $message = new Message;
            $message->contact()->associate($contact)->save();
            $message->post_id = $post->id; // this is not yet an eloquent-managed relationship
            /* WhatsApp doesn't technically come in with a message, we shall craft
            * a stand-in. */
            $message->title = "(Fulfilled WhatsApp survey)";
            /* TODO: anything more useful that could go here? */
            $message->message = "(Fulfilled WhatsApp survey)";
            $message->datetime = \DateTime::createFromFormat(\DateTime::ISO8601, $source_info['received']);
            $message->data_source = $source_info['data_source'];
            /* TODO: anything useful from ussd-engine that could go here? */
            $message->data_source_message_id = "random-" . UUID::uuid4()->toString();
            $message->type = 'whatsapp';
            $message->status = 'received';
            $message->direction = 'incoming';
            $message->notification_post_id = null;
            $message->save();

            DB::commit();

            /* The post resource should now be re-rendered, because of the
             * information added to it since creation in the parent.
             * This is not ideal performance-wise, but we'll take the hit
             * for now.
             */
            return new PostResource($post->resource);
        } catch (\Exception $e) {
            DB::rollback();
            return self::make500($e->getMessage());
        }
    }

    public function patch(int $id, Request $request)
    {
        throw new \Exception("Invalid controller method");
    }

    public function update(int $id, Request $request)
    {
        throw new \Exception("Invalid controller method");
    }

    public function delete(int $id, Request $request)
    {
        throw new \Exception("Invalid controller method");
    }
}
