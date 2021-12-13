@component('mail::message')
# Hi,

You have been invited to join the team.
**{{ $invitation->team->name }}**
Because you are not yet signed up, you will need to do so before you can join the team.
[Register for free]({{ $url }})

@component('mail::button', ['url' => $url])
Register for free
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
