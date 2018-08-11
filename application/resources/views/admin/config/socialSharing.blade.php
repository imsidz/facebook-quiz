@extends('admin/layout')

@section('content')
    <style>
        .network-item {
            padding: 0px 12px;
            margin: 3px 0px;
            color: #ffffff;
            border: solid 1px rgba(0,0,0,0.1);
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            height: 2.5em;
            line-height: 2.5em;
        }
        .network-item-placeholder {
            margin: 3px 0px;
            border: dashed 1px #ddd;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            height: 2.5em;
            line-height: 2.5em;
        }
        .network-item i{
            margin-right: 6px;
        }
        .network-sortable{
            border: dashed 1px #eee;
            padding: 10px;
            border-radius: 4px;
            min-height: 100px;
        }
        #activeNetworksSortable {
            border: solid 4px #eee;
        }
        #inactiveNetworksSortable li{
            opacity: .5;
        }
    </style>

	<!-- Page Heading -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Social sharing Config
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">

		</div>
	</div>
	<!-- /.row -->
<script>
	var socialSharingConfigData = {!! $socialSharingConfigData or 'null' !!};
    var sharingNetworks = {!! json_encode($sharingNetworks) !!};
</script>
<div class="row">
	<div class="col-md-10">
		<div class="panel panel-info">
			<div class="panel-heading">Social sharing Configuration</div>
			<div class="panel-body">
				<div class="" id="configFormContainer">
					<div class="socialSharing-form-common" action="" id="configForm">
                        <p>
                            The share buttons will be displayed in the order of networks you set here. You can drag the social networks in the active list to reorder them.
                        </p>
                        <p><b>The first two in the list will be displayed as large buttons</b></p>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Active Networks</h4>
                                <ul id="activeNetworksSortable" class="list-unstyled network-sortable">
                                    @foreach($activeNetworks as $networkKey)
                                        <li class="network-item btn-social-{{$networkKey}}" data-network="{{$networkKey}}">{{$sharingNetworkNames[$networkKey]}}</li>
                                    @endforeach
                                </ul>
                                <h1><i class="fa fa-hand-o-left pull-right text-primary"></i></h1>
                                <p>Drag networks from the inactive networks list to Active networks list <b>to activate them.</b></p>
                            </div>
                            <div class="col-md-4">
                                <h4>Inactive Networks</h4>
                                <ul id="inactiveNetworksSortable" class="list-unstyled network-sortable">
                                    @foreach($inactiveNetworks as $networkKey)
                                        <li class="network-item btn-social-{{$networkKey}}" data-network="{{$networkKey}}">{{$sharingNetworkNames[$networkKey]}}</li>
                                    @endforeach
                                </ul>
                                <h1><i class="fa fa-hand-o-right pull-left text-primary"></i></h1>
                                <p>Drag networks from the active networks list to inactive networks list <b>to deactivate them.</b></p>
                            </div>
                        </div>
                        <div class="btn btn-success save-config-btn">Save config</div>
                    </div>
					<div class="form-results-box" id="configFormResult"></div>
                    <br/><br/>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    $(function() {
        $( "#activeNetworksSortable, #inactiveNetworksSortable" ).sortable({
            connectWith: ".network-sortable",
            placeholder: "network-item-placeholder"
        }).disableSelection();
    });
</script>

<script src="{{assetWithCacheBuster('js/admin/admin.js')}}"></script>

<script>
	vent.on('config-form-submitted', function(){
		$.post('{{ route('adminConfigSocialSharing')}}', {
			socialSharingConfig: socialSharingConfigData
		}).success(function(res){
			if(res.success) {
				dialogs.success('Config Saved');
			} else if(res.error) {
				dialogs.error('Error occured\n' + res.error);
			} else {
				dialogs.error('Some Error occured');
			}
		}).fail(function(res){
			dialogs.error(res.responseText);
		});
	})
</script>

<script src="{{assetWithCacheBuster('js/admin/socialSharingConfig.js')}}"></script>

@stop