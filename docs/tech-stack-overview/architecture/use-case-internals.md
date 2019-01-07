# Use case internals

## Use Case Internals

### What is a use case?

Part of Hexagonal Architecture is the concept of the Application Boundary. This boundary separates our application as a whole from everything else \(both framework and communication with the outside world\).

> A Use Case \(sometimes called a Command\) is an explicitly defined way in which an application can be used.

We define how the outside world can communicate with our application by creating "Use Cases". These essentially are classes which name actions that can be taken. For example, our CreatePostUsecase defines that our application can create a post.

Defining Use Cases has some useful side-affects. For example, we clearly and explicitly can see how our application "wants" to be interacted with. We can plan use cases ahead of time, or add them as needed, but use cases should capture the operations which can happen within our application.

> Aside: Platform uses some generic CRUDS usecases. These aren't tied to a specific Domain Model \(Entity\) ie. a Post but rather have the entity and repo injected into them. This makes Use Cases significantly less well defined. A developer can no longer glance at the Use Case directory and see what actions are available. This might be something we can improve in future

### Anatomy of a Use Case \(in platform\)

Use Cases in platform all follow a high level interface. In short they all have a `interact()` method.

```text
interface Usecase
{
    /**
     * @return Array
     */
    public function interact();
}
```

To enable building of some generic use cases they also have `isSearch` and `isWrite` methods.

```text
interface Usecase
{
    /**
     * Will this usecase write any data?
     *
     * @return Boolean
     */
    public function isWrite();

    /**
     * Will this usecase search for data?
     *
     * @return Boolean
     */
    public function isSearch();

    /**
     * @return Array
     */
    public function interact();
}
```

The actual parameters for each UseCase are injected through setter methods, commonly: `setPayload()`, `setIdentifiers` and `setFilters`.

### CRUDS use cases

Most of our use cases follow 5 high level patterns for Create, Read, Update, Delete and Search \(CRUDS\)

#### Create

![Create Usecase](../../.gitbook/assets/create-usecase%20%281%29.png)

[create] (http://www.nomnoml.com/#view/%23title%3A

[Create UseCase|
[&lt;state&gt;request]-&gt;[Create Usecase]  
[Create Usecase]-&gt;[&lt;state&gt;response]  
[&lt;state&gt;response\]-&gt;[OutputFormatter]  
  
[&lt;state&gt;request|
payload%3B  
identifier%3B  
filters]  
  
[Create Usecase|  
     [&lt;start&gt; interact()]-&gt;[Get Entity]
     [Get Entity]-&gt;[Verify Create Auth]
     [Verify Create Auth]-&gt;[Verify Valid]
     [Verify Valid]-&gt;[Create Entity]
     [Create Entity]-&gt;[Get Created]
     [Get Created]-&gt;[&lt;choice&gt; Can Read%3F]
     [&lt;choice&gt; Can Read%3F]-&gt;[Format Entity]  
     [Format Entity]-&gt;[&lt;end&gt; return]
     [&lt;choice&gt; Can Read%3F]-&gt;[&lt;end&gt; return]  
]
 
%23direction%3A right](http://www.nomnoml.com/#view/%23title%3A%20Create%20UseCase
[<state>request]->[Create%20Usecase]
[Create%20Usecase]->[<state>response]
[<state>response]->[OutputFormatter]

[<state>request|
payload%3B
identifier%3B
filters]

[Create%20Usecase|
%20%20%20%20%20[<start>%20interact%28%29]->[Get%20Entity]
%20%20%20%20%20[Get%20Entity]->[Verify%20Create%20Auth]
%20%20%20%20%20[Verify%20Create%20Auth]->[Verify%20Valid]
%20%20%20%20%20[Verify%20Valid]->[Create%20Entity]
%20%20%20%20%20[Create%20Entity]->[Get%20Created]
%20%20%20%20%20[Get%20Created]->[<choice>%20Can%20Read%3F]
%20%20%20%20%20[<choice>%20Can%20Read%3F]->[Format%20Entity]
%20%20%20%20%20[Format%20Entity]->[<end>%20return]
%20%20%20%20%20[<choice>%20Can%20Read%3F]->[<end>%20return]
]

%23direction%3A%20right)

![Create Usecase - Collaborators](../../.gitbook/assets/create-usecase-collab.png)

 [collaborators]([http://www.nomnoml.com/\#view/%23title%3A Create UseCase Collaborators  
  
[CreateUsecase||  
interact()  
setPayload()]  
  
[Validator]&lt;-%2B[CreateUsecase]  
[Authorizer]&lt;-%2B[CreateUsecase]  
[Formatter]&lt;-%2B[CreateUsecase]  
[Repository]&lt;-%2B[CreateUsecase]  
  
[CreateUsecase]-&gt;[&lt;input&gt; payload]
  
%23direction%3A right](http://www.nomnoml.com/#view/%23title%3A%20Create%20UseCase%20Collaborators

[CreateUsecase||
interact%28%29
setPayload%28%29]

[Validator]<-%2B[CreateUsecase]
[Authorizer]<-%2B[CreateUsecase]
[Formatter]<-%2B[CreateUsecase]
[Repository]<-%2B[CreateUsecase]

[CreateUsecase]->[<input>%20payload]

%23direction%3A%20right)

#### Read

![Read Usecase](../../.gitbook/assets/read-usecase%20%282%29.png)

[read]([http://www.nomnoml.com/\#view/%23title%3A Read UseCase  
[&lt;state&gt;request]-&gt;[Read Usecase]
[Read Usecase]-&gt;[&lt;state&gt;response]
[&lt;state&gt;response]-&gt;[OutputFormatter]
  
[&lt;state&gt;request|
payload%3B
identifier%3B
filters]
  
[Read Usecase|
     [&lt;start&gt; interact()]-&gt;[Get Entity]
     [Get Entity]-&gt;[Verify Read Auth]
     [Verify Read Auth]-&gt;[Format Entity]
     [Format Entity]-&gt;[&lt;end&gt; return]
]
  
%23direction%3A right](http://www.nomnoml.com/#view/%23title%3A%20Read%20UseCase
[<state>request]->[Read%20Usecase]
[Read%20Usecase]->[<state>response]
[<state>response]->[OutputFormatter]

[<state>request|
payload%3B
identifier%3B
filters]

[Read%20Usecase|
%20%20%20%20%20[<start>%20interact%28%29]->[Get%20Entity]
%20%20%20%20%20[Get%20Entity]->[Verify%20Read%20Auth]
%20%20%20%20%20[Verify%20Read%20Auth]->[Format%20Entity]
%20%20%20%20%20[Format%20Entity]->[<end>%20return]
]

%23direction%3A%20right))

#### Update

![Update Usecase](../../.gitbook/assets/update-usecase%20%282%29.png)

[update]([http://www.nomnoml.com/#view/%23title%3A Update UseCase  
[&lt;state&gt;request]-&gt;[Update Usecase]
[Update Usecase]-&gt;[&lt;state&gt;response]
[&lt;state&gt;response]-&gt;[OutputFormatter]

[&lt;state&gt;request|
payload%3B
identifier%3B
filters]

[Update Usecase|
     [&lt;start&gt; interact()]-&gt;[Get Entity] 
     [Get Entity]-&gt;[Update State]
     [Update State]-&gt;[Verify Update Auth]
     [Verify Update Auth]-&gt;[Verify Valid]
     [Verify Valid]-&gt;[Update Entity]  
     [Update Entity]-&gt;[&lt;choice&gt; Can Read%3F]
     [&lt;choice&gt; Can Read%3F]-&gt;[Format Entity]
     [Format Entity]-&gt;[&lt;end&gt; return]  
     [&lt;choice&gt; Can Read%3F]-&gt;[&lt;end&gt; return]
]
  
%23direction%3A right](http://www.nomnoml.com/#view/%23title%3A%20Update%20UseCase
[<state>request]->[Update%20Usecase]
[Update%20Usecase]->[<state>response]
[<state>response]->[OutputFormatter]

[<state>request|
payload%3B
identifier%3B
filters]

[Update%20Usecase|
%20%20%20%20%20[<start>%20interact%28%29]->[Get%20Entity]
%20%20%20%20%20[Get%20Entity]->[Update%20State]
%20%20%20%20%20[Update%20State]->[Verify%20Update%20Auth]
%20%20%20%20%20[Verify%20Update%20Auth]->[Verify%20Valid]
%20%20%20%20%20[Verify%20Valid]->[Update%20Entity]
%20%20%20%20%20[Update%20Entity]->[<choice>%20Can%20Read%3F]
%20%20%20%20%20[<choice>%20Can%20Read%3F]->[Format%20Entity]
%20%20%20%20%20[Format%20Entity]->[<end>%20return]
%20%20%20%20%20[<choice>%20Can%20Read%3F]->[<end>%20return]
]

%23direction%3A%20right))

#### Delete

![Delete Usecase](../../.gitbook/assets/delete-usecase%20%281%29.png)

[delete]([http://www.nomnoml.com/#view/%23title%3A Delete UseCase  
[&lt;state&gt;request]-&gt;[Delete Usecase]
[Delete Usecase]-&gt;[&lt;state&gt;response]
[&lt;state&gt;response]-&gt;[OutputFormatter]
  
[&lt;state&gt;request|
payload%3B
identifier%3B
filters]

[Delete Usecase|
     [&lt;start&gt; interact()]-&gt;[Get Entity]
     [Get Entity]-&gt;[Verify Delete Auth]
     [Verify Delete Auth]-&gt;[Delete Entity]
     [Delete Entity]-&gt;[Verify Read Auth]
     [Verify Read Auth]-&gt;[Format Entity]
     [Format Entity]-&gt;[&lt;end&gt; return]
]
  
%23direction%3A right](http://www.nomnoml.com/#view/%23title%3A%20Delete%20UseCase
[<state>request]->[Delete%20Usecase]
[Delete%20Usecase]->[<state>response]
[<state>response]->[OutputFormatter]

[<state>request|
payload%3B
identifier%3B
filters]

[Delete%20Usecase|
%20%20%20%20%20[<start>%20interact%28%29]->[Get%20Entity]
%20%20%20%20%20[Get%20Entity]->[Verify%20Delete%20Auth]
%20%20%20%20%20[Verify%20Delete%20Auth]->[Delete%20Entity]
%20%20%20%20%20[Delete%20Entity]->[Verify%20Read%20Auth]
%20%20%20%20%20[Verify%20Read%20Auth]->[Format%20Entity]
%20%20%20%20%20[Format%20Entity]->[<end>%20return]
]

%23direction%3A%20right))

#### Search

![Search Usecase](../../.gitbook/assets/search-usecase.png)

[search]([http://www.nomnoml.com/#view/%23title%3A Search UseCase
[&lt;state&gt;request]-&gt;[Search Usecase]
[Search Usecase]-&gt;[&lt;state&gt;response]
[&lt;state&gt;response]-&gt;[OutputFormatter]

[&lt;state&gt;request|
payload%3B
identifier%3B
filters]

[Search Usecase|
     [&lt;start&gt; interact()]-&gt;[Get Entity]
     [Get Entity]-&gt;[Verify Search Auth]
     [Verify Search Auth]-&gt;[Set Search Params]
     [Set Search Params]-&gt;[Get Search Sesults]
     [Get Search Sesults]-&gt;[Verify Read Auth|
     [&lt;start&gt; foreach]-&gt;[&lt;choice&gt;while results%3F]
     [&lt;choice&gt;while results%3F]-&gt;[check auth]
     [check auth]-&gt;[&lt;choice&gt;while results%3F]
     [&lt;choice&gt;while results%3F]-&gt;[&lt;end&gt;]
     ]
     [Verify Read Auth]-&gt;[Format Results]
     [Format Results]-&gt;[&lt;end&gt; return]
]  

%23direction%3A right](http://www.nomnoml.com/#view/%23title%3A%20Search%20UseCase
[<state>request]->[Search%20Usecase]
[Search%20Usecase]->[<state>response]
[<state>response]->[OutputFormatter]

[<state>request|
payload%3B
identifier%3B
filters]

[Search%20Usecase|
%20%20%20%20%20[<start>%20interact%28%29]->[Get%20Entity]
%20%20%20%20%20[Get%20Entity]->[Verify%20Search%20Auth]
%20%20%20%20%20[Verify%20Search%20Auth]->[Set%20Search%20Params]
%20%20%20%20%20[Set%20Search%20Params]->[Get%20Search%20Sesults]
%20%20%20%20%20[Get%20Search%20Sesults]->[Verify%20Read%20Auth|
%20%20%20%20%20%20%20%20[<start>%20foreach]->[<choice>while%20results%3F]
%20%20%20%20%20%20%20%20[<choice>while%20results%3F]->[check%20auth]
%20%20%20%20%20%20%20%20[check%20auth]->[<choice>while%20results%3F]
%20%20%20%20%20%20%20%20[<choice>while%20results%3F]->[<end>]
%20%20%20%20%20]
%20%20%20%20%20[Verify%20Read%20Auth]->[Format%20Results]
%20%20%20%20%20[Format%20Results]->[<end>%20return]
]

%23direction%3A%20right))

