<h4>{{$currentStage->getName()}}</h4>
@if($currentStage->hasOption('banner'))
    <div style="margin: 30px auto;" class="text-center"><img src="{{$currentStage->getOption('banner')}}" alt="" style="max-width: 300px; max-height: 120px;" class="animated bounce"/></div>
@endif
<div class="panel-group" role="tablist" aria-multiselectable="true">
@foreach($currentStage->getSteps() as $step)
    @if(!$step->isMuted())
        <div class="panel panel-default step-panel @if($step->hasOutput()) collapsible @endif" id="step{{$step->getId()}}">
            <div class="panel-heading">
                <h2 class="panel-title">
                    <i class="fa status-icon fa-check-circle @if($step->hasFinished()) @if($step->hasPassed()) status-icon-success @else status-icon-error fa-times-circle @endif @endif"></i>
                    @if($step->hasOutput()) <a @if(!$step->hasPassed()) data-step-has-error="true" @endif class="collapsed" data-toggle="collapse" href="#stepBody{{$step->getId()}}" aria-expanded="false"> @endif
                        &nbsp;{{$step->getName()}}
                        @if($step->hasOutput())</a> @endif
                </h2>
            </div>
            @if($step->hasOutput())
                <div class="panel-collapse collapse step-body" id="stepBody{{$step->getId()}}">
                    <div class="panel-body">
                        {!! $step->getOutput() !!}
                    </div>
                </div>
            @endif
            @if($step->isCurrent())
                <script>
                    $(function(){
                        //$('[href="#stepBody{{$step->getId()}}"]').click();
                        $('[data-step-has-error="true"]').each(function(){
                            $(this).click();
                        });
                    });
                </script>
            @endif
        </div>
    @endif
@endforeach
    <br/><br/>
    <div class="text-center">
        @if($currentStage->getPreviousStageNumber())
            <a class="btn btn-default btn-lg" href="{{\Jitheshgopan\AppInstaller\Installer::route()}}?stage={{$currentStage->getPreviousStageNumber()}}">Prev</a>
        @endif
        @if($currentStage->getNextStageNumber())
            <a class="btn btn-success btn-lg" href="{{\Jitheshgopan\AppInstaller\Installer::route()}}?stage={{$currentStage->getNextStageNumber()}}">Next</a>
        @endif
    </div>
</div>