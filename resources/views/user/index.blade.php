@extends('layouts.app')

@section('title')
    Users
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    User Listing
                    <a style="float: right" href="{{url('/user/add')}}" >Add New User</a>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Join Date</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{$user->name}}</td>
                                <td>@if($user->role_id == 1) {{'Admin'}} @else {{'User'}} @endif </td>
                                <td>{{$user->email}}</td>
                                <td>{{date('d M, Y', strtotime($user->created_at))}}</td>
                                <td>
                                    <a href="{{ url('/user/edit/'.$user->id) }}">Edit</a>
                                    @if($user->role_id != 1)
                                        <a href="{{ url('/user/delete/'.$user->id) }}" onclick="return confirm('Are you sure?')">Delete</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
