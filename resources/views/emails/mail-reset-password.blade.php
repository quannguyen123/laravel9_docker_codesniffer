<!DOCTYPE html>
<html>
<head>
    <title>Thiết lập tài khoản của bạn trên trang tuyển dụng VietnamWorksn</title>
</head>
<body>
    <p>
        Xin chào {{ $details['user']['name'] }},
    </p>

    <p>
    {{ $details['user']['name'] }} đã yêu cầu cấp lại mật khẩu mới.
    </p>

    <p>Nếu đây là một nhầm lẫn vui lòng bỏ qua email này.</p>

    <p>
        Xin vui lòng nhấp vào đường link bên dưới để thiết lập mật khẩu mới cho tài khoản của bạn
    </p>

    <div style="text-align:center">
        <a href="{{ $details['user']['url'] }}"
            style="color:#fff;background:#ff9800;font-weight:bold;font-size:14px;text-decoration:none;border-color:#ff9800;border-width:12px;border-left-width:25px;border-right-width:25px;border-style:solid;" 
            target="_blank">
            Đổi mật khẩu
        </a>
        
        <p>
            Nếu bạn có bất kỳ thắc mắc nào liên quan đến tài khoản, hãy tham khảo trang trợ giúp hoặc gửi email đến chúng tôi: contact@vietnamworks.com.
        </p>
        
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