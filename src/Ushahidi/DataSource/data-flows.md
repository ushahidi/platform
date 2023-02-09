# DataSource Data flows

## Receive Callback

Controller --> Storage::receive() --> ReceiveMessageUsecase::interact()

## Poll Send

Controller --> Storage::getPending() --> Storage::updateStatus() --> Response

## Send

OutgoingCli --> each(source) <----- loop --													<---\
                  | 																			|
                  \--> Storage::getPending --> Source::send() --> Storage::updateMessage()  ----/

## Fetch

IncomingCli --> each(source) <----- loop --															<---\
                  | 																					|
                  \--> Source::fetch() --> Storage::receive --> ReceiveMessageUsecase::interact()   ----/

