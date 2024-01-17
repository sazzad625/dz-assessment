<table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
       style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
    <tr>
        <td>
            <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                   align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="height:80px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:center;">

                        <h2 style="font-weight: bolder;"><strong>{{config('app.name')}}</strong></h2>

                    </td>
                </tr>
                <tr>
                    <td style="height:20px;">&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                               style="max-width:670px;background:#fff; border-radius:3px; text-align:left;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                            <tr>
                                <td style="height:40px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="padding:0 35px;">
                                    <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;">
                                        Welcome to OPS Academy</h1>
                                    <span
                                        style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                                    <h2>Hey there,</h2>
                                    <p style="color:#455056; font-size:15px;line-height:24px; margin:0; margin: auto;">
                                        We're excited to have you get started. Click the button below to set the
                                        password for your account.
                                    </p>
                                    <p>
                                        User name: <b>{{$userName}}</b>
                                    </p>
                                    <span
                                        style="background:#54bbfb;text-decoration:none !important;text-align: center; font-weight:500;margin: auto; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;width: 300px; height: 40px; line-height: 40px;display:block;">
                                        <a href="{{route('password.reset', $token)}}" style="color: #fff; text-decoration: none;">Set Password</a></span><br><br>
                                    <p style="color:#455056; font-size:15px;line-height:24px; margin:0; margin: auto;">
                                        If that doesn't work, copy and paste the following link in your browser.
                                        <br/><br/>
                                        {{route('password.reset', $token)}}
                                        <br><br>Please
                                        do not reply to this email as this is a system generated email.
                                    </p><br>
                                    <p style="color:#455056; font-size:15px;line-height:24px; margin:0; margin: auto;">
                                        Thank you,
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td style="height:40px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                <tr>
                    <td style="height:20px;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align:center;">
                        <p style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;">
                            &copy; <strong>{{config('app.url')}}. All rights reserved</strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="height:80px;">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
