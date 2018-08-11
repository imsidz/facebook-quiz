@extends('admin/layout')

@section('content')
	<style>
		#widgetPlacementsContainer img {
			max-width: 100%;
		}
		[name="disabled"] + .toggle .toggle-on{
			background: #FF5252;
		}
	</style>
	<!-- Page Heading -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Widgets
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">

		</div>
	</div>
	<!-- /.row -->
<script>
	var widgetsSchema = {!! $widgetsSchema or 'null' !!};
	var widgetItemSchema = widgetsSchema.items.properties.widgets.items.properties;
	var widgetsData = {!! $widgetsData or 'null' !!};
	var lastSavedWidgetsDataJson= JSON.stringify(widgetsData);
</script>
<div class="row">
	<div class="col-md-10">
		<div class="panel panel-info">
			<div class="panel-heading">Widgets</div>
			<div class="panel-body">
				{{--<div class="" id="widgetsFormContainer">
					<form class="quiz-form-common" action="" id="widgetsForm"></form>
					<div class="form-results-box" id="widgetsFormResult"></div>
				</div>--}}

						<div id="widgetPlacementsContainer">

						</div>

				<div class="btn btn-success btn-lg save-changes-btn">Save changes</div>
			</div>
		</div>
	</div>
</div>

<script>
	window.partial = function(which, data) {
		var tmpl = $('#' + which).html();
		return _.template(tmpl)(data);
	};
	window.getWidgetFormOptions = function () {
		return {};
	}
</script>

<script id="widgetItemTemplate" type="text/template">
	<div class="panel panel-default widget-item" data-widget-index="<%= index %>" data-widget-placement-id="<%= widgetPlacement.id %>">
		<div class="panel-heading clearfix">
			<div class="panel-title">
				<%= widget.title || "NO TITLE" %> <% if(widget.disabled == true || widget.disabled == 'true'){ %> <div class="label label-danger" style="border-radius: 30px;">Disabled</div> <% } %><div class="btn btn-default btn-sm pull-right widget-edit-toggle"><i class="fa fa-pencil"></i></div>
			</div>
		</div>
		<div class="panel-body hidden">
			<form class="widget-form" action=""></form>
		</div>
	</div>
</script>
<script type="text/template" id="widgetPanelTemplate">
	<% _.each(widgetPlacements, function(widgetPlacement){ %>
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-primary widget-placement-item" data-widget-placement-id="<%= widgetPlacement.id %>">
					<div class="panel-heading"><%= widgetPlacement.name %></div>
					<div class="panel-body widget-items-container">
						<% _.each(widgetPlacement.widgets, function(widget, index){ %>
							<%= partial('widgetItemTemplate', {widgetPlacement: widgetPlacement, widget: widget, index: index}) %>

						<% }) %>
					</div>
					<div class="panel-footer">
						<div class="btn btn-default add-new-widget-btn"><i class="fa fa-plus"></i> Add new</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<p><b>Position reference:</b></p>
				<img src="{{asset('images/widget-placement-previews')}}/<%= widgetPlacement.id %>.jpg" alt=""/>
			</div>
		</div>
		<hr/>
	<% }) %>
</script>

<script src="{{assetWithCacheBuster('js/admin/admin.js')}}"></script>

<script>
	function getWidgetPlacementValue(placementId){
		return _.findWhere(widgetsData.widgets, {'id':placementId});
	}
	function getWidgetValue(placementId, index){
		var widgetPlacementValue = getWidgetPlacementValue(placementId);
		var widgetValue = {};
		if(widgetPlacementValue && widgetPlacementValue.widgets && typeof widgetPlacementValue.widgets[index] != "undefined")
			widgetValue = widgetPlacementValue.widgets[index];
		return widgetValue;
	}


	function setWidgetsValue(placementId, value){
		var widgetPlacementValue = getWidgetPlacementValue(placementId);
		widgetPlacementValue.widgets = value;
		return value;
	}

	function setWidgetsItemValue(placementId, index, value){
		var widgetPlacementValue = getWidgetPlacementValue(placementId);
		widgetPlacementValue.widgets[index] = value;
		return value;
	}

	function renderWidgetsEditor() {
		var widgetPanelTemplate = $('#widgetPanelTemplate').html();
		var panelHtml = _.template(widgetPanelTemplate)({widgetPlacements: widgetsData.widgets});
		$('#widgetPlacementsContainer').html(panelHtml);
		$( ".widget-items-container" ).sortable({
			update: function(){
				var container = $(this);
				var newWidgetsValue = [];
				var widgetPlacementId,
					index;
				container.children('.widget-item').each(function(){
					widgetPlacementId = $(this).data('widgetPlacementId');
					index = $(this).data('widgetIndex');
					newWidgetsValue.push(getWidgetValue(widgetPlacementId, index));
				});
				setWidgetsValue(widgetPlacementId, newWidgetsValue);
				renderWidgetsEditor();
			}
		});
		//$( ".widget-items-container" ).disableSelection();
	}

	function contractWidgetItem(widgetItem){
		var panelBody = widgetItem.children('.panel-body');
		panelBody.addClass('hidden');
	}
	function removeWidgetItem(widgetItem){
		var widgetPlacementId = widgetItem.data('widgetPlacementId');
		var widgetIndex = widgetItem.data('widget-index');
		var widgetPlacementValue = getWidgetPlacementValue(widgetPlacementId);
		widgetPlacementValue.widgets.splice(widgetIndex, 1);
		renderWidgetsEditor();
	}
	function expandWidgetItem(widgetItem) {
		var panelBody = widgetItem.children('.panel-body');
		panelBody.removeClass('hidden');
		var widgetForm = widgetItem.find('.widget-form');

		var widgetPlacementId = widgetItem.data('widgetPlacementId');
		var widgetIndex = widgetItem.data('widget-index');
		//console.log( widgetPlacementId, widgetIndex);
		var widgetPlacementValue = getWidgetPlacementValue(widgetPlacementId);
		var widgetValue = {};
		if(widgetPlacementValue && widgetPlacementValue.widgets && typeof widgetPlacementValue.widgets[widgetIndex] != "undefined")
			widgetValue = widgetPlacementValue.widgets[widgetIndex];
		var formOptions = getFormViewOptions(widgetItemSchema, {
			events: {
				results: {
					onChange: function(e, node){

					},
					onInsert: function(e, node){

					}
				}
			},
			formOptions: getWidgetFormOptions()
		});

		_.findWhere(formOptions, {type: "actions"}).items.push({
				type: "button",
				title: '<i class="fa fa-times"></i> Cancel',
				onClick: function(evt){
						evt.preventDefault();
					contractWidgetItem(widgetItem);
				}
			},
			{
				type: "button",
				title: '<i class="fa fa-trash-o"></i> Delete',
				htmlClass: "btn-danger",
				onClick: function(evt){
					evt.preventDefault();
					removeWidgetItem(widgetItem);
					return false;
				}
			}
		);

		widgetForm.html('');
		widgetForm.jsonForm({
			schema: widgetItemSchema,
			form: formOptions,
			value: widgetValue,
			onSubmit: function (errors, values) {
				if (errors) {
					$('#widgetsFormResult').html('<p>I beg your pardon?</p>');
				}
				else {
					widgetPlacementValue.widgets[widgetIndex] = values;
					vent.trigger('hideForm', 'widgetsForm');
				}
				vent.trigger('widgets-form-submitted');
			}
		});
	}

	vent.on('widgets-form-submitted', function(){
		renderWidgetsEditor();
	});
	$('.save-changes-btn').click(function(){
		vent.trigger('widgets-form-submitted');
	});

	$(function(){
		renderWidgetsEditor();

		$('body').on('click', '.add-new-widget-btn', function(){
			var placementItem = $(this).parents('.widget-placement-item');
			var placementId = placementItem.data('widgetPlacementId');

			var placementValue = getWidgetPlacementValue(placementId);
			placementValue.widgets = placementValue.widgets || [];
			placementValue.widgets.push({});
			renderWidgetsEditor();

			var placementItem = $('.widget-placement-item[data-widget-placement-id="'+ placementId +'"]');
			var container = placementItem.find('.widget-items-container');
			expandWidgetItem(container.find('.widget-item').last());
		});

		$('body').on('click', '.widget-edit-toggle', function(){
			var widgetItem = $(this).parents('.widget-item')
			expandWidgetItem(widgetItem);
		});
	});
	vent.on('widgets-form-submitted', function(){
		$.post(BASE_PATH + '/admin/config/widgets', {
			widgets: widgetsData
		}).success(function(res){
			if(res.success) {
				lastSavedWidgetsDataJson= JSON.stringify(widgetsData);
				dialogs.success('Widgets Saved');
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

<script>
	function renderWidgetsForm() {
		vent.trigger('widgetsFormForm:beforeRender');
		var formOptions = getFormViewOptions({
				widgets: widgetsSchema
			}, {
			events: {
				results: {
					onChange: function(e, node){

					},
					onInsert: function(e, node){

					}
				}
			}
		});

		$('#widgetsFormContainer').find('#widgetsForm, .form-results-box').html('');
		$('#widgetsForm').jsonForm({
			schema: {
				widgets: widgetsSchema
			},
			form: formOptions,
			value: {
				widgets: widgetsData.widgets
			},
			onSubmit: function (errors, values) {
			  if (errors) {
				$('#widgetsFormResult').html('<p>I beg your pardon?</p>');
			  }
			  else {
				  widgetsData = values;
				  vent.trigger('hideForm', 'widgetsForm');
			  }
				vent.trigger('widgets-form-submitted');
			}
		});
	}

	renderWidgetsForm();
	window.onbeforeunload = function(){
		var newWidgetsDataJson = JSON.stringify(widgetsData);
		if(lastSavedWidgetsDataJson != newWidgetsDataJson) {
			return "You have made some changes in the widgets that are not saved. You have to click the 'Save changes' button at the bottom to save the changes.";
		}
	};
</script>

@stop


@section('foot')
	@parent
	<link rel="stylesheet" href="{{ assetWithCacheBuster('/bower_components/jquery-ui/themes/base/sortable.css')}}"/>
	<script src="{{ assetWithCacheBuster('/bower_components/jquery-ui/ui/minified/sortable.min.js') }}"></script>
@stop
