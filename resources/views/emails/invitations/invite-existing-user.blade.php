@component('mail::message')
# Hi,

You have been invited to join the team.
**{{ $invitation->team->name }}**
Because you are a member of the team, you can now access the team's dashboard.

@component('mail::button', ['url' => $url])
Go to dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
