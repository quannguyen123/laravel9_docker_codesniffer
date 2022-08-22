<!DOCTYPE html>
<html>
<body>

<h2>User Login</h2>

<form action="{{ route('user-login') }}" method="POST">
  @csrf
  <label>Email</label><br>
  <input type="text" name="email"><br>
  <label>Password</label><br>
  <input type="password" name="password"><br>
  <input type="submit" value="Submit">
</form> 

</body>
</html>
