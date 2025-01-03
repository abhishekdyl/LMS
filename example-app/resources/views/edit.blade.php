
<x-app-layout>

<div class="container m-5">
    <h1>Update User</h1>
    <form action="/edit" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$upuser->id}}">
        <label for="name">Name : </label>
        <input type="text" name="name" id="name" value="{{$upuser->name}}">
        @if($errors->has('name'))
            <span class='text-danger' >{{ $errors->first('name') }}</span>
        @endif<br/><br/>

        <label for="roles">Role :</label>
        <select name="roles" id="roles">
          <option {{($upuser->roleid == 0) ? 'selected' : '' }} value="0">User</option>
          <option {{($upuser->roleid == 1) ? 'selected' : '' }} value="1">admin</option>
        </select><br/><br/>

        <label for="email">Email : </label>
        <input type="text" name="email" id="email" value="{{$upuser->email}}">
        @if($errors->has('email'))
            <span class='text-danger' >{{ $errors->first('email') }}</span>
        @endif<br/><br/>
        <label for="address">Address : </label>
        <input type="text" name="address" id="address" value="{{$upuser->address}}"><br/><br/>
        <label for="number">Contact : </label>
        <input type="text" name="number" id="number" value="{{$upuser->contact}}"><br/><br/>        
        <label for="profile_img">Profile Image : </label>
        <input type="file" name="profile_img" id="profile_img" >
        @if($errors->has('profile_img'))
            <span class='text-danger' >{{ $errors->first('profile_img') }}</span>
        @endif<br/><br/><br/>

        <input type="submit" class="btn-success p-2 rounded-1" value="update">
        <a href="/dashboard/createuser" class="btn-danger p-2 rounded-1"> Cancel </a>    
    </form>
</div>

</x-app-layout>