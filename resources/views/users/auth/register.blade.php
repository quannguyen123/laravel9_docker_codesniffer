<!DOCTYPE html>
<html>
<body>

<h2>User Register</h2>

<form action="{{ route('user-register') }}" method="POST">
    @csrf
    <label>Username</label><br>
    <input type="text" name="name"><br>
    <label>Email</label><br>
    <input type="text" name="email"><br>
    <label>Password</label><br>
    <input type="password" name="password"><br>
    <label>Password Confirm</label><br>
    <input type="password" name="password_confirmation"><br>
    <input type="submit" value="Submit">
</form> 

</body>
</html>
