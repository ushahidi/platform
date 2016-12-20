FROM alpine:3.4

RUN apk add --no-cache python py-pip && \
    pip install awscli
