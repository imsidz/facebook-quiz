<div class="text-center" style="margin: 30px;">
    <i class="fa fa-5x fa-check-circle status-icon status-icon-success animated bounce"></i>
    <h3>Installation successful</h3>
    @if(!empty($proceedUrl))
        <br/><a class="btn btn-success" href="{{$proceedUrl}}">{{$proceedUrlText}}</a>
    @endif
</div>