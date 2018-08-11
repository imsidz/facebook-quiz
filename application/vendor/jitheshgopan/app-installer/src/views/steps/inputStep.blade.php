<div>
    <form id="form-Step{{$step->getId()}}" action=""></form>
    <div class="form-submit-response"></div>
</div>


<script>
    $(function(){
        var formData = @if($step->getData()){!! json_encode($step->getData()) !!} @else undefined @endif;
        var $form = $('#form-Step{{$step->getId()}}');
        var $responseBox = $form.siblings('.form-submit-response');
        var fieldsSchema = {!! json_encode($fieldsSchema) !!};
        function onFormSuccess(values){
            $.post('{{route(\Jitheshgopan\AppInstaller\Installer::config('routeName'))}}',{
                stage: '{{$currentStage->getStageNumber()}}',
                action: 'saveStepData',
                step: '{{$step->getId()}}',
                data: values
            }).success(function(res){
                window.location.href = window.location.href;
            }).fail(function(res){
                alert(res.responseText);
            });
        }

        function onFormError(errors){
            $responseBox.html('<p>Some error occured</p>');
        }

        $form.jsonForm({
            schema: fieldsSchema,
            value : formData,
            onSubmit: function (errors, values) {
                if (errors) {
                    onFormError(errors);
                }
                else {
                    onFormSuccess(values);
                }
            }
        });
    })
</script>