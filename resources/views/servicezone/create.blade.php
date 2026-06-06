<x-master-layout>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{__('messages.service_zone_configuration')}}</h5>
                            <a href="{{route('servicezone.index')}}" class="float-end btn btn-sm btn-primary">
                                <i class="fa fa-angle-double-left"></i> {{__('messages.back')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{__('messages.guidelines_to_create_zone')}}</h5>

                <p><strong class="text-primary">{{__('messages.Step_1')}}</strong> {{__('messages.create_zone_by_clicking_on_the_map_and_connect_the_dots_together')}}</p>
                <p><strong class="text-primary">{{__('messages.Step_2')}}</strong> {{__('messages.use_this')}}
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.6875 6.25008C14.3228 6.24955 13.9638 6.3409 13.6438 6.5157C13.5281 6.15744 13.3219 5.83509 13.0452 5.57984C12.7685 5.32458 12.4306 5.14506 12.0642 5.05864C11.6978 4.97223 11.3153 4.98183 10.9536 5.08654C10.592 5.19124 10.2636 5.3875 10 5.65633C9.69586 5.34592 9.30616 5.13313 8.88059 5.0451C8.45502 4.95708 8.01288 4.9978 7.61056 5.16209C7.20823 5.32637 6.86395 5.60676 6.62163 5.96751C6.37931 6.32825 6.24994 6.753 6.25 7.18758V8.75008H5.3125C4.73234 8.75008 4.17594 8.98054 3.7657 9.39078C3.35547 9.80102 3.125 10.3574 3.125 10.9376V11.8751C3.125 13.6984 3.84933 15.4471 5.13864 16.7364C6.42795 18.0257 8.17664 18.7501 10 18.7501C11.8234 18.7501 13.572 18.0257 14.8614 16.7364C16.1507 15.4471 16.875 13.6984 16.875 11.8751V8.43758C16.875 7.85741 16.6445 7.30102 16.2343 6.89078C15.8241 6.48054 15.2677 6.25008 14.6875 6.25008ZM15.625 11.8751C15.625 13.3669 15.0324 14.7977 13.9775 15.8526C12.9226 16.9074 11.4918 17.5001 10 17.5001C8.50816 17.5001 7.07742 16.9074 6.02252 15.8526C4.96763 14.7977 4.375 13.3669 4.375 11.8751V10.9376C4.375 10.6889 4.47377 10.4505 4.64959 10.2747C4.8254 10.0988 5.06386 10.0001 5.3125 10.0001H6.25V11.8751C6.25 12.0408 6.31585 12.1998 6.43306 12.317C6.55027 12.4342 6.70924 12.5001 6.875 12.5001C7.04076 12.5001 7.19973 12.4342 7.31694 12.317C7.43415 12.1998 7.5 12.0408 7.5 11.8751V7.18758C7.5 6.93894 7.59877 6.70048 7.77459 6.52466C7.9504 6.34885 8.18886 6.25008 8.4375 6.25008C8.68614 6.25008 8.9246 6.34885 9.10041 6.52466C9.27623 6.70048 9.375 6.93894 9.375 7.18758V9.37508C9.375 9.54084 9.44085 9.69981 9.55806 9.81702C9.67527 9.93423 9.83424 10.0001 10 10.0001C10.1658 10.0001 10.3247 9.93423 10.4419 9.81702C10.5592 9.69981 10.625 9.54084 10.625 9.37508V7.18758C10.625 6.93894 10.7238 6.70048 10.8996 6.52466C11.0754 6.34885 11.3139 6.25008 11.5625 6.25008C11.8111 6.25008 12.0496 6.34885 12.2254 6.52466C12.4012 6.70048 12.5 6.93894 12.5 7.18758V9.37508C12.5 9.54084 12.5658 9.69981 12.6831 9.81702C12.8003 9.93423 12.9592 10.0001 13.125 10.0001C13.2908 10.0001 13.4497 9.93423 13.5669 9.81702C13.6842 9.69981 13.75 9.54084 13.75 9.37508V8.43758C13.75 8.18894 13.8488 7.95048 14.0246 7.77466C14.2004 7.59885 14.4389 7.50008 14.6875 7.50008C14.9361 7.50008 15.1746 7.59885 15.3504 7.77466C15.5262 7.95048 15.625 8.18894 15.625 8.43758V11.8751Z" fill="#6C757D"/>
                    </svg>
                    {{__('messages.to_drag_the_map_and_find_the_proper_area')}}
                </p>
                <p><strong class="text-primary">{{__('messages.Step_3')}}</strong> Click the polygon draw button (pentagon icon) on the left side of the map to start drawing your zone. Click on the map to place points, then click the first point to close the polygon. Minimum 3 points required.
                    <br>
                    <span class="text-muted"></span>
                </p>
            </div>
        </div>


        <!-- Full Width Card -->
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h2 class="mb-4">{{ $pageTitle }}</h2>

                    <form method="POST" action="{{ route('servicezone.store') }}" id="servicezone" data-toggle="validator">
                        @csrf
                        @if(isset($servicezone->id))
                            <input type="hidden" name="id" value="{{ $servicezone->id }}">
                        @endif

                        <div class="form-group mb-3">
                            <label for="zone_name">{{__('messages.zone_name')}} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="zone_name" class="form-control" placeholder="{{__('messages.enter_zone_name')}}" value="{{ $servicezone->name ?? old('name') }}" required>
                            <small class="help-block with-errors text-danger"></small>
                        </div>

                        <div class="form-group mb-3">
                            <label>{{__('messages.draw_zone_on_map')}} <span class="text-danger">*</span></label>
                            <div id="map" style="height: 500px; width: 100%; border: 1px solid #ddd; border-radius: 4px; z-index: 1;"></div>
                            <input type="hidden" name="coordinates" id="coordinates" value="{{ isset($servicezone->coordinates) ? json_encode($servicezone->coordinates) : (old('coordinates') ? old('coordinates') : '[]') }}" required>
                            <small class="help-block with-errors text-danger" id="coordinate-error"></small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">{{__('messages.status')}}</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ (isset($servicezone) && $servicezone->status == 1) ? 'selected' : '' }}>{{__('messages.active')}}</option>
                                <option value="0" {{ (isset($servicezone) && $servicezone->status == 0) ? 'selected' : '' }}>{{__('messages.inactive')}}</option>
                            </select>
                        </div>

                        @if(auth()->user()->can('service zone add'))
                            <button type="submit" class="btn btn-md btn-primary float-end">{{__('messages.save')}}</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    {{-- Leaflet Draw CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    {{-- Leaflet Draw JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

    {{-- Custom styles for map type switcher --}}
    <style>
        .map-type-control {
            background: white;
            border-radius: 4px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.4);
            padding: 0;
            overflow: hidden;
        }
        .map-type-control button {
            display: block;
            width: 100%;
            border: none;
            background: white;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            color: #333;
            text-align: left;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        .map-type-control button:last-child {
            border-bottom: none;
        }
        .map-type-control button:hover {
            background: #f0f0f0;
        }
        .map-type-control button.active {
            background: #e8f0fe;
            color: #1a73e8;
            font-weight: 600;
        }
    </style>

    <script>
        (function() {
            let map;
            let drawnItems;
            let drawControl;
            let currentPolygon = null;

            // Google Maps API key from .env
            const GOOGLE_API_KEY = '{{ env("GOOGLE_MAPS_API_KEY") }}';

            // Parse existing coordinates from the hidden input
            let existingCoordinates = {!! isset($servicezone->coordinates) ? json_encode($servicezone->coordinates) : 'null' !!};

            function initLeafletMap() {
                // Default center: India
                let centerLat = 20.5937;
                let centerLng = 78.9629;
                let zoomLevel = 5;

                // Initialize the map
                map = L.map('map').setView([centerLat, centerLng], zoomLevel);

                // Google Maps tile layers (accurate maps using your API key)
                var googleRoadmap = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&key=' + GOOGLE_API_KEY, {
                    maxZoom: 22,
                    attribution: '&copy; Google Maps'
                });

                var googleSatellite = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}&key=' + GOOGLE_API_KEY, {
                    maxZoom: 22,
                    attribution: '&copy; Google Maps'
                });

                var googleHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&key=' + GOOGLE_API_KEY, {
                    maxZoom: 22,
                    attribution: '&copy; Google Maps'
                });

                var googleTerrain = L.tileLayer('https://mt1.google.com/vt/lyrs=p&x={x}&y={y}&z={z}&key=' + GOOGLE_API_KEY, {
                    maxZoom: 22,
                    attribution: '&copy; Google Maps'
                });

                // Set default layer to Roadmap
                googleRoadmap.addTo(map);

                // Add layer control for switching map types (top-right)
                var baseMaps = {
                    "Roadmap": googleRoadmap,
                    "Satellite": googleSatellite,
                    "Hybrid": googleHybrid,
                    "Terrain": googleTerrain
                };
                L.control.layers(baseMaps, null, { position: 'topright', collapsed: false }).addTo(map);

                // Initialize the FeatureGroup to store editable layers
                drawnItems = new L.FeatureGroup();
                map.addLayer(drawnItems);

                // Initialize the draw control
                drawControl = new L.Control.Draw({
                    position: 'topleft',
                    draw: {
                        polygon: {
                            allowIntersection: false,
                            showArea: true,
                            shapeOptions: {
                                color: '#3388ff',
                                weight: 2,
                                fillOpacity: 0.3
                            }
                        },
                        polyline: false,
                        circle: false,
                        rectangle: false,
                        marker: false,
                        circlemarker: false
                    },
                    edit: {
                        featureGroup: drawnItems,
                        remove: true
                    }
                });
                map.addControl(drawControl);

                // If editing an existing zone, load the polygon
                if (existingCoordinates && Array.isArray(existingCoordinates) && existingCoordinates.length > 0) {
                    try {
                        console.log('Loading existing coordinates:', existingCoordinates);
                        
                        // Convert {lat, lng} objects to [lat, lng] arrays for Leaflet
                        const latLngs = existingCoordinates.map(function(coord) {
                            return [parseFloat(coord.lat), parseFloat(coord.lng)];
                        });

                        currentPolygon = L.polygon(latLngs, {
                            color: '#3388ff',
                            weight: 2,
                            fillOpacity: 0.3
                        });

                        drawnItems.addLayer(currentPolygon);

                        // Fit the map to the polygon bounds
                        map.fitBounds(currentPolygon.getBounds().pad(0.1));
                    } catch (e) {
                        console.error('Error loading existing coordinates:', e);
                    }
                }

                // Handle new polygon creation
                map.on(L.Draw.Event.CREATED, function(event) {
                    var layer = event.layer;

                    // Remove the previous polygon if any
                    if (currentPolygon) {
                        drawnItems.removeLayer(currentPolygon);
                    }

                    currentPolygon = layer;
                    drawnItems.addLayer(layer);
                    updateCoordinatesFromLayer(layer);
                });

                // Handle polygon edit
                map.on(L.Draw.Event.EDITED, function(event) {
                    var layers = event.layers;
                    layers.eachLayer(function(layer) {
                        updateCoordinatesFromLayer(layer);
                    });
                });

                // Handle polygon delete
                map.on(L.Draw.Event.DELETED, function(event) {
                    currentPolygon = null;
                    document.getElementById('coordinates').value = '[]';
                });
            }

            function updateCoordinatesFromLayer(layer) {
                var latLngs = layer.getLatLngs()[0]; // Get the first ring of the polygon
                var coordinates = latLngs.map(function(latlng) {
                    return {
                        lat: latlng.lat,
                        lng: latlng.lng
                    };
                });
                document.getElementById('coordinates').value = JSON.stringify(coordinates);
                console.log('Coordinates updated:', coordinates);
            }

            // Initialize map when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                initLeafletMap();

                // Form validation on submit
                document.getElementById('servicezone').addEventListener('submit', function(e) {
                    var coordInput = document.getElementById('coordinates');
                    var coords = coordInput.value;
                    var errorBlock = document.getElementById('coordinate-error');

                    try {
                        var parsedCoords = JSON.parse(coords);

                        if (!Array.isArray(parsedCoords) || parsedCoords.length < 3) {
                            e.preventDefault();
                            if (errorBlock) {
                                errorBlock.textContent = "{{ __('messages.please_draw_zone') }}";
                            }
                        } else {
                            if (errorBlock) {
                                errorBlock.textContent = '';
                            }
                        }
                    } catch (err) {
                        e.preventDefault();
                        if (errorBlock) {
                            errorBlock.textContent = "{{ __('messages.invalid_coordinates') }}";
                        }
                    }
                });
            });
        })();
    </script>
    @endsection

</x-master-layout>