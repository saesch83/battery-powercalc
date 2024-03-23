<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Power-Ratings" activePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->        
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">                        
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0 table-striped">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                USV-Anlage</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                DC-50</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                DC-100</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                DC-200
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                DC-450
                                            </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                DC>400
                                            </th>                                   
                                        </tr>
                                    </thead>
                                    <tbody>                                        
                                        @foreach ($anlagenleistungen as $leistungen)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{$leistungen["USVname"]}}</h6>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                @foreach($leistungen[50] as $leistung)
                                                <span class="text-secondary text-sm">{{$leistung}}kw </span><br>
                                                @endforeach
                                            </td>
                                            <td class="align-middle text-center">
                                                @foreach($leistungen[100] as $leistung)
                                                <span class="text-secondary text-sm">{{$leistung}}kw </span><br>
                                                @endforeach
                                            </td>
                                            <td class="align-middle text-center">
                                                @foreach($leistungen[200] as $leistung)
                                                <span class="text-secondary text-sm">{{$leistung}}kw </span><br>
                                                @endforeach
                                            </td>
                                            <td class="align-middle text-center">
                                                @foreach($leistungen[400] as $leistung)
                                                <span class="text-secondary text-sm">{{$leistung}}kw </span><br>
                                                @endforeach
                                            </td>
                                            <td class="align-middle text-center">
                                                @foreach($leistungen["max"] as $leistung)
                                                <span class="text-secondary text-sm">{{$leistung}}kw </span><br>
                                                @endforeach
                                            </td>
                                            <!--<td class="align-middle">
                                                <a rel="tooltip" class="btn btn-success btn-link"
                                                    href="" data-original-title=""
                                                    title="">
                                                    <i class="material-icons">edit</i>
                                                    <div class="ripple-container"></div>
                                                </a>
                                                
                                                <button type="button" class="btn btn-danger btn-link"
                                                data-original-title="" title="">
                                                <i class="material-icons">close</i>
                                                <div class="ripple-container"></div>
                                            </button>
                                            </td>-->
                                        </tr>
                                        @endforeach                                   
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

</x-layout>