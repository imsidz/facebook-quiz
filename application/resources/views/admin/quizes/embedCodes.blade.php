@extends('admin/layout')

@section('content')

    <script id="embedElementCodeTemplate" type="text/template"><div class="x-quizzes-list-widget" data-limit="<%= limit %>" data-stream="<%= stream %>"></div></script>
<br><br>
<div class="panel panel-green">
    <div class="panel-heading">
        <div class="panel-title">
            <strong>Step 1</strong>
        </div>
    </div>
	<div class="panel-body">
        <form action="" method="post" role="form" class="embed-code-customize-form">
        	<legend>Choose options</legend>

        	<div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Enter number of quizzes to be shown</label>
                        <input type="text" class="form-control" name="limit" id="quizzesLimit" placeholder="" value="10">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">List order</label>
                        <select name="stream" id="quizzesListOrder" class="form-control">
                        	<option value="latest">Latest</option>
                            <option value="popular">Popular</option>
                            <option value="random">Random</option>
                        </select>
                    </div>
                </div>
            </div>

        </form>
        <h4>Copy this code to wherever you want the quizzes list to appear</h4>
        <textarea name="" id="embedElementCode" cols="80" rows="6">
</textarea>
        <script>

            (function() {
                var customizeFields = $('.embed-code-customize-form .form-control'),
                        codeTemplate = $('#embedElementCodeTemplate').html(),
                        codeField = $('#embedElementCode');

                customizeFields.on('change', function() {
                    var data = {};
                    customizeFields.each(function() {
                        data[$(this).attr('name')] = $(this).val();
                    });
                    codeField.val(_.template(codeTemplate)(data));
                });
                customizeFields.trigger('change');
            })();
        </script>
	</div>
</div>


<div class="panel panel-green">
    <div class="panel-heading">
        <div class="panel-title">
            <strong>Step 2</strong>
        </div>
    </div>
    <div class="panel-body">
    <h3>Copy this code to just above the "{{{ '</body>'}}}" tag</h3>
    <textarea name="" id="" cols="60" rows="8">
@include('admin.quizes.partials.embedQuizzesListCode')
</textarea>
    </div>
</div>
<br/><br/>

@stop