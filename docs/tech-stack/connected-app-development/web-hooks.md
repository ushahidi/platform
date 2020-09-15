# Web hooks

When a report is created or updated, Ushahidi Platform can send a POST request to a third- party application listening to HTTP requests on the internet.

In order to set up your Web hook, you may do this from the web client as an administrator. You will find a web hook section in the deployment's settings panel \(you may also type in a direct URL to go there i.e. https://deployment.example.com**/settings/webhooks** \).

While adding your web hook, you are given the following options:

* **Name**: descriptive name for the web hook
* **Shared Secret** _**\(optional\)**_: a shared secret that will be used to sign the web hook payload. Your application receiving the web hook call may check this signature to verify that the web hook payload was originated in the Platform.
* **API URL**: the URL that Platform should send the web hook callbacks to. For each event, this URL will be used for sending a POST request, with a payload similar to the one you will find in the example below.
* **Event Type**: you may choose to subscribe your web hook to create or update events
* **Entity Type**: only subscription to events related to posts entity are available at this point.
* **Enable Source and Destination fields**: this allows you to pinpoint two fields from a specific survey. The web hook payload will specifically feature the attribute IDs of the selected fields.

{% hint style="info" %}
The source and destination field selection is only meant as an assistance for third party applications that perform data transformation.

By featuring the fields that are relevant to the data transformation process, these fields' keys don't need to be configured into the third party application, and there's no necessity to look up the form definition to find them. 
{% endhint %}

## Example payload

### Headers

```text
Content-type: application/json
Accept: application/json
X-Ushahidi-Signature: ...
```

### Body

```text
{
  "webhook_uuid": ...,
  "form_id": ...,
  "values": {
  },
  "source_field_key": ...,
  "destination_field_key": ...
}
```

## Payload processing

### Signature checking

If you set a shared secret while you are setting up the web hook, the callbacks sent from the API will contain a `X-Ushahidi-Signature` header that you may check in order to be certain that ...

1. ... the request is indeed coming from the Platform API that you configured
2. ... and that the contents of the callback have not been tampered with

The signature is the base-64 encoded result of running the HMAC keyed hash value method, with the following parameters:

* SHA256 hashing algorithm
* Shared secret as secret key

The data passed to the HMAC hashing function is the string concatenation of

* the web hook callback URL \(as configured under "API URL" in the Platform's settings\)
* the callback body JSON data

Running the HMAC hash on the web hook receiving side should match the result with the contents of the  `X-Ushahidi-Signature` header.

### Expected response

The third party application should provide the following response to the web hook request posted from Platform API.

#### Headers & response code

```text
HTTP 200 OK
Content-type: application/json
```

#### Body

```text
{ "status": 200 }
```

## Delivery conditions

* **No guaranteed delivery**. There is no warranty that events will be received and processed by the receiving end. Transient network or other problems may cause some events to not fire properly or remain unprocessed.
* **Deferred delivery**. Delivery of web hook callbacks is not made in real time. With a given frequency, the Platform will go through any pending events to emit and send their payloads. The frequency is determined by how often the background cron job is configured to execute.

