<h4>{{\Jitheshgopan\AppInstaller\Installer::trans('installer.stages')}}</h4>
<div class="list-group">
    @foreach($stages as $stage)
        <a class="list-group-item @if($stage->isActive()) active @endif @if($stage->isComplete()) completed @endif" href="" disabled>{{$stage->getName()}} <i class="fa status-icon @if($stage->hasError()) fa-times-circle status-icon-error @else fa-check-circle @endif pull-right"></i></a>
    @endforeach
</div>