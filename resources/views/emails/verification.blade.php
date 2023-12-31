<div
    style="background: linear-gradient(187.16deg, #181623 0.07%, #191725 51.65%, #0D0B14 98.75%); color:white; padding-left: 10%; padding-right:10%; font-family: Tahoma, Verdana, sans-serif; padding-top: 15%; padding-bottom: 15%;">
    <div style="margin:auto; left:0;right:0; width:fit-content;">
        <img src="{{ asset('Vector.svg') }}" />
    </div>

    <div style="margin:auto; left:0;right:0; width:fit-content;">MOVIE QUOTES</div>
    <p style="margin-top: 30px">Hola {{ $username }}</p>

    <p style="margin-top: 30px">Thanks for joining Movie quotes! We really appreciate it. Please click the button below
        to verify your account:</p>
    <a href="{{ $url }}"
        style="display: block; width: 128px; max-width: 392px; background: #E31221; border-radius: 4px; text-align: center; line-height: 38px; text-decoration: none; color: #ffffff;">Verify
        account</a>

    <p style="margin-top: 40px; ">If clicking doesn't work, you can try copying and pasting it to your browser:</p>

    <p style="overflow-wrap: break-word;">
        {{ $url }}
    </p>

    <p>If you have any problems, please contact us: support@moviequotes.ge</p>
    <p>MovieQuotes Crew</p>
</div>
