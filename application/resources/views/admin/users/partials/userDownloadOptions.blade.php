@if(!empty($users))
    <div class="col-md-4">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <h3 class="panel-title">Download Emails</h3>
            </div>
            <div class="panel-body">
                <div id="downloadEmailsSection">
                    <h4>Total users: {{$users->total()}}</h4>

                    <form action="{{ Helpers::getUrlWithQuery() }}" method="post">
                        <div class="checkbox">
                            <label for="">
                                <input name="includeName" checked="checked" type="checkbox"> Include name
                            </label>
                        </div>
                        <div class="radio">
                            <label for="">
                                <input name="displayOnScreen" value="false" checked="checked" type="radio"> Save as file
                            </label>
                        </div>
                        <div class="radio">
                            <label for="">
                                <input name="displayOnScreen" type="radio" value="true"> Display on screen
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="">Limit</label><br>
                            <input type="text" class="form-action" name="downloadLimit">
                        </div>
                        <input type="hidden" name="download" value="true">
                        <input type="submit" value="Download emails" class="form-action btn btn-success">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif