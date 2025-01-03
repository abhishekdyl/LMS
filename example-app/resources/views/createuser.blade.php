


<x-app-layout>
        <div class="m-5">
            <h1>Create Users</h1>
            <form class="d-flex justify-content-between" action="createusercontroller" method="post" enctype="multipart/form-data">
                <div clas="field">
                    @csrf
                    <label for="name">Name : </label>
                    <input type="text" name="name" id="name" placeholder="enter your name">
                    @if($errors->has('name'))
                        <span class='text-danger' >{{ $errors->first('name') }}</span>
                    @endif
                    <label for="email">Email : </label>
                    <input type="text" name="email" id="email" placeholder="enter your email" autocomplete="off">
                    @if($errors->has('email'))
                        <span class='text-danger' >{{ $errors->first('email') }}</span>
                    @endif
                    <label for="address">Password : </label>
                    <input type="password" name="password" id="password" autocomplete="off">
                    <label for="address">Address : </label>
                    <input type="text" name="address" id="address" placeholder="enter your address">
                    <label for="number">Contact : </label>
                    <input type="text" name="number" id="number" placeholder="enter your contect">
                    <label for="profile_img">Profile Image : </label>
                    <input type="file" name="profile_img" id="profile_img">
                    @if($errors->has('profile_img'))
                        <span class='text-danger' >{{ $errors->first('profile_img') }}</span>
                    @endif
                </div>
                <div class="sub-btn">
                    <input class="btn-success p-2 rounded-1" type="submit" value="Submit">
                </div>
            </form>


            <table class="table table-striped">

                <thead>
                    <tr> 
                        <th>ID</th>
                        <th></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($datausers as $key => $user)
                    <tr>
                        <td>{{$user->id}}</td>
                        <!-- <td><img src="/products/{{$user->user_img}}" class="rounded-pill" width="125px" height="100px" alt="img"></td> -->
                        <td><img src="{{$user->profile_photo_path}}" class="rounded-pill" width="125px" height="100px" alt="img"></td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->address}}</td>
                        <td>{{$user->contact}}</td>
                        <td><a class="btn-primary text-light p-1 p-2 rounded" href="/dashboard/update/{{$user->id}}">edit</a>&nbsp;<a class="btn-danger text-light p-1 p-2 rounded" href="/dashboard/delete/{{$user->id}}">del</a></td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
</x-app-layout>

