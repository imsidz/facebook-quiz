<?php

$configurePluginUrl = action('\AdminPluginsController@getConfigure', [$slug]);
    $translatePluginUrl = action('\AdminPluginsController@getTranslate', [$slug]);
    $isInstalled   =   App::make('plugins')->isInstalled($slug);
    $isActive   =   App::make('plugins')->isEnabled($slug);
    $isConfigurable   =   App::make('plugins')->isConfigurable($slug);
    $isTranslatable   =   App::make('plugins')->isTranslatable($slug);
    $pluginImages = App::make('plugins')->getPluginImages($slug);
    $activateEndPoint = action('\AdminPluginsController@postActivate', [$slug]);
    $installEndPoint = action('\AdminPluginsController@postInstall', [$slug]);
    $uninstallEndPoint = action('\AdminPluginsController@postUninstall', [$slug]);
    $plugin = Plugins::getPlugin($slug);
?>
<div class="col-md-4">
    <div class="box box-solid plugin-box">
    	  <div class="box-body">
              <div class="plugins-preview-block" >
                  <div class="plugins-preview-carousel carousel slide" data-ride="carousel" data-interval="2000">
                      <!-- Wrapper for slides -->
                      <div class="carousel-inner" role="listbox">
                          @forelse($pluginImages as $i => $image)
                              <div class="item @if($i == 0) active @endif" style="background-image: url({{content_url($image)}})">
                              </div>
                          @empty
                              <div class="item active">
                                <div class="preview-placeholder">
                                    <i class="fa fa-5x fa-image"></i>
                                </div>
                              </div>
                          @endforelse
                      </div>
                  </div>
              </div>
              <div class="plugin-details">
                  <h3 class="plugin-title">{{$plugin->get('name')}}</h3>
                  <p class="text-muted small">{{$plugin->get('description')}}</p>
              </div>
              @if($plugin->get('url') || $plugin->get('links'))
                  <div class="plugin-links">
                      @if($plugin->get('url'))
                          <a class="plugin-link" href="{{$plugin->get('url')}}"><i class="fa fa-globe"></i>&nbsp; Website</a>
                      @endif
                      @if($plugin->get('links'))
                          @foreach($plugin->get('links') as $link)
                              <a class="plugin-link" href="{{$link['url']}}">
                                    @if(!empty($link['icon']))
                                        <i class="fa fa-{{$link['icon']}}"></i>&nbsp;
                                    @endif
                                    {{$link['text']}}
                                </a>
                          @endforeach
                      @endif
                  </div>
              @endif
    	  </div>
            <div class="box-footer">
                <div class="clearfix plugin-activation-container">
                    @if($isInstalled)
                        <h4 class="plugin-status-legend pull-left">Status</h4>
                        <div class="plugin-enable-toggle item-activator pull-right" data-item-id="{{$slug}}" data-end-point="{{$activateEndPoint}}" data-toggle="tooltip" title="Enable/Disable" data-activating-message="Enabling" data-activated-message="Enabled" data-deactivating-message="Disabling" data-deactivated-message="Disabled">
                            <input type="checkbox" value="true" name="" @if($isActive) checked="checked" @endif/>
                            <div class="toggle toggle-light" data-toggle-width="80" @if($isActive) data-toggle-on="true" @endif></div>
                        </div>
                    @else
                        <h4 class="plugin-status-legend pull-left">Not installed</h4>
                        <span class="plugin-install-btn btn btn-success btn-xs pull-right" data-item-id="{{$slug}}" data-end-point="{{$installEndPoint}}"><i class="fa fa-download"></i>&nbsp; Install</span>
                    @endif
                </div>
                <div class="clearfix">
                    @if($isConfigurable)
                        <a href="{{$configurePluginUrl}}" class="btn btn-default btn-xs"><i class="fa fa-gears"></i>&nbsp; Settings</a>
                    @endif
                    @if($isTranslatable)
                        <a href="{{$translatePluginUrl}}" class="btn btn-default btn-xs"><i class="fa fa-language"></i>&nbsp; Languages</a>
                    @endif
                    @if($isInstalled)
                        <span class="plugin-uninstall-btn btn btn-danger btn-xs pull-right" style="margin-left: 5px;" data-item-id="{{$slug}}" data-end-point="{{$uninstallEndPoint}}" data-toggle="tooltip" title="Uninstall"><i class="fa fa-times"></i> Uninstall</span>
                    @endif
                </div>
            </div>
    </div>
</div>
