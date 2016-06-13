@extends('layouts.app')
@section('filter')
     <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>Saringan (Filter)</strong></h3>
            </div>
            <div class="panel-body">
                {!! Form::open(array('route' => 'complain.index', 'method'=>'GET', 'id'=>'form1')) !!}
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label">Carian:</label>
                        {!! Form::text('search_anything',Request::get('search_anything'),array('class'=>'form-control','placeholder'=>'Carian')) !!}
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">Aduan ID:</label>
                        {!! Form::text('aduan_id',Request::get('aduan_id'),array('class'=>'form-control','placeholder'=>'Aduan ID')) !!}
                    </div>
                    <div class="col-sm-2 col-xs-10">
                        <label class="control-label">Status:</label>
                        {!! Form::select('complain_status_id',$get_statuses,'',['class'=> 'form-control','id'=>'complain_status_id'])!!}
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">Tarikh Mula:</label>
                        {!! Form::text('start_date',Request::get('start_date'),array('class'=>'form-control datepicker','placeholder'=>'Tarikh Mula')) !!}
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">Tarikh Akhir:</label>
                        {!! Form::text('end_date',Request::get('end_date'),array('class'=>'form-control datepicker','placeholder'=>'Tarikh Akhir')) !!}
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="submit_type" id="submit_type" value="search"/>
                        <label class="control-label"> </label><br>
                        <button type="submit" class="btn btn-primary" id="search" name="search">Filter Rekod</button>
                    </div>

                </div>
                {!! Form::close() !!}
            </div>
        </div>
@endsection

@section('content')
    @include('layouts.alert_message')

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Senarai Aduan</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-10 col-xs-2">
                    <a href="{{route('complain.create')}}" class="btn btn-warning">Tambah Aduan</a>
                    <a href="#" class="dropdown-toggle btn btn-default" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        10 <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#">9</a></li>
                        <li><a href="#">8</a></li>
                        <li><a href="#">7</a></li>
                    </ul>
                </div>
                <div class="col-sm-2 col-xs-10">
                    <input type="text" class="form-control" placeholder="Cari..." aria-describedby="basic-addon1">
                </div>
            </div>
            <br>
            <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th>Pengadu</th>
                    <th>Aduan id</th>
                    <th>Aduan / Tindakan</th>
                    <th>Tarikh</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                    <th></th>

                </tr>
                @foreach($complains as $complain)
                <tr>
                    <td>
                        {{ $complain->employeeU_fk->short_name or $complains->user_emp_id}}
                    </td>
                    <td>{{$complain->complain_id}}</td>
                    <td>Aduan: {{str_limit($complain->complain_description,50)}}<br><hr>
                        Tindakan: {{$complain->action_comment}}
                    </td>
                    <td>{{$complain->created_at->format('d-m-Y H:i:s')}}</td>
                    <td>
                        {{--tengok model function mutator--}}
                        {!! $complain->status !!}

                    </td>
                    <td>
                        {{ $complain->employeeT_fk->short_name or $complain->action_emp_id}}
                    </td>
                    <td>
                        {!! Form::open(array('route' => ['complain.destroy',$complain->complain_id],'method'=>'delete', 'class'=>"form-horizontal")) !!}

                        @if($complain->complain_status_id==1)
                            {{--if members can edit complain when status is BARU--}}
                            @if(Entrust::can('action_complain') )  {{--<-untuk Helpdesk--}}
                                <a href="{{route('complain.action',$complain->complain_id)}}" class="btn btn-warning">
                                    <span class="glyphicon glyphicon-edit"></span> Kemaskini</a>
                            @elseif(Entrust::can('edit_complain') && ($complain->register_user_id==Auth::user()->emp_id
                                                                  ||  $complain->user_emp_id==Auth::user()->emp_id) )                                  {{--<-untuk yang ada edit complain shj--}}
                                <a href="{{route('complain.edit',$complain->complain_id)}}" class="btn btn-warning">
                                    <span class="glyphicon glyphicon-edit"></span> Kemaskini</a>
                            @else  {{--<-untuk orang lain--}}
                                <a href="{{route('complain.show',$complain->complain_id)}}" class="btn btn-info">
                                    <span class="glyphicon glyphicon-eye-open"></span>  Papar</a>
                            @endif
                            @if(Entrust::can('delete_complain') AND $complain->complain_status_id==1)
                                <button type="button" class="btn btn-danger" data-destroy><span class="glyphicon glyphicon-trash"></span> Hapus</button>
                            @endif
                        @elseif($complain->complain_status_id==2)
                            {{--if members can edit complain when status is TINDAKAN--}}
                            @if((Entrust::can('technical_action_complain') && ($complain->register_user_id!=Auth::user()->emp_id || $complain->user_emp_id!=Auth::user()->emp_id))
                             || (Entrust::can('action_complain') && ($complain->action_emp_id==Auth::user()->emp_id))) {{--<-untuk Technical team & Helpdesk--}}
                                <a href="{{route('complain.technical_action',$complain->complain_id)}}" class="btn btn-warning">
                                    <span class="glyphicon glyphicon-edit"></span> Kemaskini</a>
                            @else   {{--orang lain xleh edit.. bagi tengok jer--}}                                {{--<-untuk yang ada edit complain shj--}}
                                <a href="{{route('complain.show',$complain->complain_id)}}" class="btn btn-info">
                                    <span class="glyphicon glyphicon-eye-open"></span> Papar</a>
                            @endif
                        @elseif($complain->complain_status_id==3)
                            {{--if members can edit complain when status is SAHKAN(P)--}}
                            @if(Entrust::can('verify_complain_action') && ($complain->register_user_id==Auth::user()->emp_id || $complain->user_emp_id==Auth::user()->emp_id)) {{--<-untuk Pengesahan Pengadu--}}
                                <a href="{{route('complain.edit',$complain->complain_id)}}" class="btn btn-warning">
                                    <span class="glyphicon glyphicon-edit"></span> Pengesahan</a>
                            @else
                                <a href="{{route('complain.show',$complain->complain_id)}}" class="btn btn-info">
                                    <span class="glyphicon glyphicon-eye-open"></span> Papar</a>
                            @endif
                        @elseif($complain->complain_status_id==4)
                            {{--if members can edit complain when status is SAHKAN(H)--}}
                            @if(Entrust::can('close_complain'))  {{--<-untuk Pengesahan Pengadu--}}
                            <a href="{{route('complain.action',$complain->complain_id)}}" class="btn btn-warning">
                                <span class="glyphicon glyphicon-edit"></span> Pengesahan Helpdesk</a>
                            @else
                                <a href="{{route('complain.show',$complain->complain_id)}}" class="btn btn-info">
                                    <span class="glyphicon glyphicon-eye-open"></span> Papar</a>
                            @endif

                        @elseif($complain->complain_status_id==6)
                            {{--if members can edit complain when status is HAPUS--}}

                        @elseif($complain->complain_status_id==7)
                            {{--if members can edit complain when status is HAPUS--}}
                            @if(Entrust::can('assign_complain'))  {{--<-untuk Pengesahan Pengadu--}}
                            <a href="{{route('complain.assign_staff',$complain->complain_id)}}" class="btn btn-warning">
                                <span class="glyphicon glyphicon-user"></span> Agihan</a>
                            @else
                                <a href="{{route('complain.show',$complain->complain_id)}}" class="btn btn-info">
                                    <span class="glyphicon glyphicon-eye-open"></span> Papar</a>
                            @endif
                        @else
                            {{--can view only--}}
                            <a href="{{route('complain.show',$complain->complain_id)}}" class="btn btn-info">
                                <span class="glyphicon glyphicon-eye-open"></span> Papar</a>

                        @endif

                        {{-- Letak checking if role!= helpdesk, hide delete button--}}
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach
            </table>
            <nav class="navbar-form navbar-right">
                {{ $complains->appends(Request::except('page'))->links() }}
            </nav>
        </div>
        </div>
    </div>
@endsection