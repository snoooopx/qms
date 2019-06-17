@extends('layouts.default')
@section('content')

    <h1>Welcome to QMS</h1>
    <form action="/" method="get" name="filterForm" id="filterForm">

        <div class="container">

            <div class="row">
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="country" id="country" class="form-control">
                                <option value="">Select Country</option>
                                @foreach($countryList as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="sex" id="sex" class="form-control">
                                <option value="">Select Sex</option>
                                <option value="m">Male</option>
                                <option value="f">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success" id="filter">Filter</button>
                        </div>
                    </div>
                    <div class="raw">
                        <br>
                        <div class="">
                            <p><b>Filtered: </b> <span id="filteredResult"></span></p>
                            <p><b>Total: </b> <span id="totalResult"></span></p>
                        </div>
                        <br>
                        <h4>Create Mail Group</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="mailGroup" id="mailGroup" placeholder="Group name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <button type="button" id="sendToQueue" class="btn btn-info form-control">Send Messages</button>
                            </div>
                        </div>
                        <div class="row">
                            <p><span id="queueStatus"></span></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <h4>Current Jobs</h4>
                    <table class="table table-bordered" id="queuesTable">
                        <thead>
                        <th>Name</th>
                        <th>Queued Massages</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>

@stop