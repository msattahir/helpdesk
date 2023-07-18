<!DOCTYPE html>
<html>

<head>
    <title>Helpdesk Support</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .header {
            /* background-color: #006717; */
            background-image: radial-gradient(circle, #444444, #006717);
            text-align: center;
            padding: 20px;
            color: #ffffff;
            position: relative;
        }

        .header::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-left: 25px solid transparent;
            border-right: 25px solid transparent;
            border-top: 25px solid #444444;
        }

        .logo img{
            width: 100px;
            height: 100px;
            margin: 0 auto;
            background-image: radial-gradient(circle, #ffffff, #444444);
            border-radius: 50%;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }

        .body {
            background-color: #f5f5f5;
            padding: 20px;
            margin: 40px;
            border-radius: 20px;
        }

        .highlight {
            font-weight: bold;
            color: #006717;
        }

        .footer {
            background-color: #dddddd;
            padding: 40px;
            text-align: center;
        }

    </style>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="{{ asset('assets/img/logos/logo-short.png') }}" alt="NCDMB Logo">
        </div>
        <h1 class="title">Helpdesk Support</h1>
    </div>

    <div class="body">
        <p>Dear {{ $staff->name }} ({{ $staff->staff_no }}),</p>
        <p>
            You've been assigned a helpdesk support ticket for:<br><br>
            Department: <span class="highlight">{{ $ddd->short }} - {{ $ddd->name }}</span><br>
            Floor: <span class="highlight">{{ $ddd->floor }}</span><br><br>
            Please address promptly.
        </p>
        <p>
            Best regards, <br>
            Helpdesk Admin
        </p>
    </div>

    <div class="footer">
        <p>
            Thank you for your support. If you have any questions, please contact the helpdesk admin at
            <span class="highlight">ict-helpdesk@ncdmb.gov.ng</span>.
        </p>
    </div>
</body>

</html>
