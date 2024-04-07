<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <x-navbars.sidebar activePage="batterycalc"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="{{__('Calculation UPS Battery')}}"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <form method="POST" action="{{ route('batterycalcShow') }}">
                @csrf
            <div class="row" style="height: calc(100vh - 230px)">
                <div class="container">
                    <div class="row">
                        <div class="card card-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="input-group input-group-static">
                                                <label for="anlagen" class="ms-0">@lang("UPS System")</label>
                                                <select class="form-control" name="usv_leistungs_id">
                                                    @foreach($formData["anlagen"] as $anlage)                                            
                                                            <option value="{{ $anlage["id"] }}" @selected(old('anlage') == $anlage['id'])>{{ $anlage["anlage"]["name"] }} {{ $anlage["dcleistung"]}} kv</option>
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group input-group-static">
                                                <label for="load" class="ms-0">@lang("Load")</label>
                                                <select class="form-control" name="load">
                                                    @foreach($formData["load"] as $load)                                            
                                                            <option value="{{ $load }}" @selected(old('load') == $load)>{{ $load }}%</option>                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">                           
                                        <div class="col-lg-6">
                                            <div class="input-group input-group-static">
                                                <label for="time" class="ms-0">@lang("Backup Time")</label>
                                                <select class="form-control" name="autonomiezeit">
                                                    @foreach($formData["time"] as $time)                                            
                                                            <option value="{{ $time }}" @selected(old('time') == $time)>{{ $time }} Minutes</option>                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group input-group-static">
                                                <label for="time" class="ms-0">@lang("End of discharge cell voltage")</label>
                                                <select class="form-control" name="entladeschlussspanung">
                                                    @foreach($formData["cellvoltage"] as $voltage)                                            
                                                            <option value="{{ $voltage }}" @selected(old('voltage') == $voltage)>{{ $voltage }} V/cell</option>                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">                           
                                        <div class="col-lg-6">
                                            <div class="input-group input-group-static">
                                                <label for="time" class="ms-0">@lang("Time tolerance 'from'")</label>
                                                <select class="form-control" name="autonomiezeitmin">
                                                    @foreach($formData["tolerance"] as $percent)                                            
                                                            <option value="{{ $percent }}" @selected(old('tolerancefrom') == $percent)>{{ $percent }}%</option>                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group input-group-static">
                                                <label for="time" class="ms-0">@lang("Time tolerance 'to'")</label>
                                                <select class="form-control" name="autonomiezeitmax">
                                                    @foreach($formData["tolerance"] as $percent)                                            
                                                            <option value="{{ $percent }}" @selected(old('toleranceto') == $percent)>{{ $percent }}%</option>                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                    </div>                  
                                </div>
                                <div class="col-lg-4">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-group input-group-static">
                                                <label for="load" class="ms-0">@lang("Temperature")</label>
                                                <select class="form-control" name="temperature" disabled>
                                                    <option value=20>20°C</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="input-group input-group-static">
                                                <label for="anlagen" class="ms-0">@lang("Battery cabinets")</label>
                                                <select class="form-control" name="schrank">
                                                    @foreach($formData["schraenke"] as $schrank)                                            
                                                            <option value="{{ $schrank["id"] }}" @selected(old('schrank') == $schrank["id"])>{{ $schrank["name"] }}</option>                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="col-lg-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="input-group input-group-static">
                                                <label for="time" class="ms-0">@lang("Min battery strings")</label>
                                                <select class="form-control" name="min_straenge">
                                                    @foreach($formData["strings"] as $string)                                            
                                                            <option value="{{ $string }}" @selected(old('minstring') == $string)>{{ $string }}</option>                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group input-group-static">
                                                <label for="time" class="ms-0">@lang("Max battery strings")</label>
                                                <select class="form-control" name="max_straenge">
                                                    @foreach($formData["strings"] as $string)
                                                            <option value="{{ $string }}" @selected(old('maxstring') == $string)>{{ $string }}</option>                                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                    </div>               
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-group input-group-static">
                                                <label for="anlagen" class="ms-0">@lang("Manufacturer")</label>
                                                <select class="form-control" name="hersteller_id">
                                                    @foreach($formData["hersteller"] as $herstell)                                            
                                                            <option value="{{ $herstell["id"] }}" @selected(old('hersteller') == $herstell['id'])>{{ $herstell["name"] }}</option>                                        
                                                    @endforeach        
                                                </select>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>                            
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="input-group input-group-static">
                                        <button type="submit" class="btn btn-secondary">@lang("Calculate")</button> 
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div>       
            </div>
            </form>           
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

</x-layout>
