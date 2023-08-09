<!DOCTYPE html>
<html>
<head>
    <title>Thiết lập tài khoản của bạn trên trang tuyển dụng VietnamWorksn</title>
</head>
<body>
    <p>
        Xin chào {{ $details['job']['recipients_of_cv'] }},
    </p>

    <p>
    {{ $details['user']['first_name'] }} {{ $details['user']['last_name'] }} đã gửi được hồ sơ ứng tuyển cho công việc của bạn
    </p>

    <p>
        Bạn vui lòng kiểm tra hồ sơ và liên hệ với ứng viên để sắp xếp thời gian phỏng vấn.
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