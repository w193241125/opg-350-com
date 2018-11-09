
<div class="portlet light bordered formBox" id="editPmBox">
    <div class="portlet-title">
        <div class="caption font-green">
            <i class="icon-pin font-green"></i>
            <span class="caption-subject bold uppercase">编辑用户权限</span>
        </div>
        <div class="actions">
            <a class="btn btn-circle btn-icon-only btn-default close-link">
                <i class="fa fa-times"></i>
            </a>
        </div>
    </div>
    <div class="portlet-body form">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>信息填写出错!</strong>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
        @endif
            <form role="form" method="post" id="editPermissionForm" action="{{route('updUserPermission')}}" >
                {{ csrf_field() }}
                <input type="hidden" name="uid" value="{{ $uid }}">
                <div class="form-body">
                    <div class="form-group form-md-checkboxes">
                        <div class="col-md-offset-1 col-md-10">
                            @if(!empty($permisson_html))
                                <div class="portlet light portlet-fit bordered">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="icon-settings font-red"></i>
                                            <span class="caption-subject font-red sbold uppercase">用户权限</span>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-scrollable table-scrollable-borderless">
                                            <table class="table table-hover table-light">
                                                <thead>
                                                <tr class="uppercase">
                                                    <th class="col-md-1 text-center"> 模块 </th>
                                                    <th class="col-md-11 text-center"> 权限 </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                {!! $permisson_html !!}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-5 col-md-7">
                            <input type="submit" class="btn green editPermissionButton" value="更新用户">
                        </div>
                    </div>
                </div>
            </form>
    </div>
</div>
<script type="text/javascript">

</script>