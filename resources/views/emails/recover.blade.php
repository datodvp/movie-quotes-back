<div
    style="background: linear-gradient(187.16deg, #181623 0.07%, #191725 51.65%, #0D0B14 98.75%); color:white; padding-left: 10%; padding-right:10%; font-family: Tahoma, Verdana, sans-serif; padding-top: 15%; padding-bottom: 15%;">
    <div style="margin:auto; left:0;right:0; width:fit-content;">
        <img src="{{ asset('Vector.svg') }}" />
    </div>

    <div style="margin:auto; left:0;right:0; width:fit-content;">MOVIE QUOTES</div>
    <p style="margin-top: 30px">Hola {{ $user->username }}</p>

    <p style="margin-top: 30px">We have sent you a password recovery link. Please click the button below:</p>
    <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}"
        style="display: block; width: fit-content; padding-left: 15px; padding-right:15px; background: #E31221; border-radius: 4px; text-align: center; line-height: 38px; text-decoration: none; color: #ffffff;">Recover
        password</a>

    <p style="margin-top: 40px;">If you have any problems, please contact us: support@moviequotes.ge</p>
    <p>MovieQuotes Crew</p>
</div>
