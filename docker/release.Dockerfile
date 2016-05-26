FROM golang:1.5.3

RUN go get github.com/aktau/github-release

COPY docker/release.run.sh /release.run.sh

ENTRYPOINT [ "/bin/bash", "/release.run.sh" ]
