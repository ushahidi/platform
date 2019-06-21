workflow "New workflow" {
  on = "issues"
  resolves = ["GitHub Action for Slack"]
}

action "GitHub Action for Slack" {
  uses = "Ilshidur/action-slack@6aeb2acb39f91da283faf4c76898a723a03b2264"
  secrets = ["SLACK_WEBHOOK"]
  args = "Issue "{{ EVENT_PAYLOAD.issue.title }}" was {{ EVENT_PAYLOAD.action }} by {{ EVENT_PAYLOAD.sender.login }} - {{ EVENT_PAYLOAD.issue.html_url }}"
}
