<!DOCTYPE html>
<html>
<head>
    <title>Thiết lập tài khoản của bạn trên trang tuyển dụng VietnamWorksn</title>
</head>
<body>
    <p>
        Hi {{ $details['user']['first_name'] }} {{ $details['user']['last_name'] }},
    </p>

    <p>
        {{ $details['company']['name'] }} đã nhận được hồ sơ ứng tuyển của bạn
    </p>

    <p>
        Hồ sơ của bạn đã được gửi đến {{ $details['company']['name'] }}. Nhà Tuyển Dụng sẽ cân nhắc và liên hệ với bạn trong thời gian sớm nhất nếu hồ sơ của bạn đáp ứng yêu cầu tuyển dụng.
    </p>

    <p>
        Job title: {{ $details['job']['job_title'] }}
    </p>
    <div style="text-align:center">
        <p>
            Thân mến,
        </p>
        <p>
            Phòng Dịch Vụ Khách Hàng
        </p>
        <p>
            VietnamWorks
        </p>
        <p>
            ``````````````````````````````````````````
        </p>
    </div>
    <p>Thank you</p>
</body>
</html>