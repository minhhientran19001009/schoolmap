@php
    $lat = $initialLat ?? 20.2506;
    $lng = $initialLng ?? 105.9745;
@endphp

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
.loc-picker-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    margin: 8px 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    font-family: inherit;
    box-sizing: border-box;
}
:is(.dark) .loc-picker-card {
    background-color: #18181b;
    border-color: #27272a;
    color: #f4f4f5;
}
.loc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
:is(.dark) .loc-header {
    border-bottom-color: #27272a;
}
.loc-title {
    font-size: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #0f172a;
    margin: 0;
}
:is(.dark) .loc-title {
    color: #ffffff;
}
.loc-desc {
    font-size: 12px;
    color: #64748b;
    margin: 4px 0 0 0;
}
:is(.dark) .loc-desc {
    color: #94a3b8;
}
.loc-btn-gps {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #10b981, #0d9488);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    transition: all 0.15s ease;
    white-space: nowrap;
}
.loc-btn-gps:hover {
    background: linear-gradient(135deg, #059669, #0f766e);
}
.loc-btn-gps:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.loc-search-row {
    display: flex;
    gap: 8px;
    align-items: center;
    margin: 12px 0;
}
.loc-input-wrapper {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
}
.loc-input-icon {
    position: absolute;
    left: 10px;
    width: 18px !important;
    height: 18px !important;
    color: #94a3b8;
    pointer-events: none;
    flex-shrink: 0;
}
.loc-input {
    width: 100%;
    box-sizing: border-box;
    padding: 8px 12px 8px 36px;
    font-size: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background-color: #f8fafc;
    color: #0f172a;
    outline: none;
    height: 38px;
}
:is(.dark) .loc-input {
    background-color: #27272a;
    border-color: #3f3f46;
    color: #f4f4f5;
}
.loc-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    background-color: #ffffff;
}
:is(.dark) .loc-input:focus {
    background-color: #18181b;
}
.loc-btn-apply {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 600;
    color: #ffffff;
    background-color: #4f46e5;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    white-space: nowrap;
    height: 38px;
    transition: background-color 0.15s;
}
.loc-btn-apply:hover {
    background-color: #4338ca;
}
.loc-map-wrapper {
    position: relative;
    width: 100%;
    height: 380px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #cbd5e1;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.06);
    box-sizing: border-box;
}
:is(.dark) .loc-map-wrapper {
    border-color: #3f3f46;
}
.loc-map-element {
    width: 100% !important;
    height: 380px !important;
    min-height: 380px !important;
    background-color: #f1f5f9;
    z-index: 1;
}
.loc-map-hint {
    position: absolute;
    bottom: 10px;
    left: 10px;
    z-index: 400;
    background-color: rgba(255, 255, 255, 0.92);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 500;
    color: #334155;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    border: 1px solid rgba(226, 232, 240, 0.8);
    pointer-events: none;
}
:is(.dark) .loc-map-hint {
    background-color: rgba(24, 24, 27, 0.92);
    color: #cbd5e1;
    border-color: #3f3f46;
}
.loc-status-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
    padding: 8px 12px;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 12px;
}
:is(.dark) .loc-status-bar {
    background-color: #27272a;
    border-color: #3f3f46;
}
.loc-coord-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    background-color: #ede9fe;
    color: #6d28d9;
    font-weight: 600;
    font-size: 11px;
    font-family: monospace;
    white-space: nowrap;
}
:is(.dark) .loc-coord-badge {
    background-color: #3b0764;
    color: #d8b4fe;
}
.loc-status-text {
    flex: 1;
    color: #475569;
    font-size: 12px;
    overflow: hidden;
    text-overflow: ellipsis;
}
:is(.dark) .loc-status-text {
    color: #cbd5e1;
}
.loc-inline-svg {
    display: inline-block;
    vertical-align: middle;
    flex-shrink: 0;
}
.loc-spin {
    animation: locSpinAnim 0.75s linear infinite;
}
@keyframes locSpinAnim {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<div 
    wire:ignore
    x-data="schoolLocationPicker({ 
        defaultLat: {{ $lat }}, 
        defaultLng: {{ $lng }} 
    })" 
    x-init="initComponent()"
    class="loc-picker-card"
>
    <!-- Header & Quick actions -->
    <div class="loc-header">
        <div>
            <h3 class="loc-title">
                <svg class="loc-inline-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"></path>
                    <circle cx="12" cy="9" r="2.5"></circle>
                </svg>
                Xác định Vị trí trên Bản đồ
            </h3>
            <p class="loc-desc">
                Dán tọa độ Google Maps hoặc bấm "Lấy địa điểm hiện tại" để hệ thống tự động ghim và nhận diện địa chỉ chi tiết.
            </p>
        </div>

        <!-- Nút Lấy vị trí hiện tại -->
        <button 
            type="button" 
            @click="getCurrentLocation()"
            :disabled="isLocating"
            class="loc-btn-gps"
        >
            <svg x-show="!isLocating" class="loc-inline-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <circle cx="12" cy="12" r="3"></circle>
                <line x1="12" y1="2" x2="12" y2="6"></line>
                <line x1="12" y1="18" x2="12" y2="22"></line>
                <line x1="2" y1="12" x2="6" y2="12"></line>
                <line x1="18" y1="12" x2="22" y2="12"></line>
            </svg>
            <svg x-show="isLocating" class="loc-inline-svg loc-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="1"></path>
            </svg>
            <span x-text="isLocating ? 'Đang lấy GPS...' : '📍 Lấy địa điểm hiện tại'"></span>
        </button>
    </div>

    <!-- Thanh dán tọa độ Google Maps -->
    <div class="loc-search-row">
        <div class="loc-input-wrapper">
            <svg class="loc-input-icon loc-inline-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <input 
                type="text" 
                x-model="pasteInput" 
                @keydown.enter.prevent="applyPastedCoordinates()"
                @paste="setTimeout(() => applyPastedCoordinates(), 100)"
                placeholder="Dán tọa độ Google Maps (VD: 20.2506, 105.9745) hoặc link Google Maps..." 
                class="loc-input"
            />
        </div>
        <button 
            type="button" 
            @click="applyPastedCoordinates()"
            class="loc-btn-apply"
        >
            <svg class="loc-inline-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            Định vị ngay
        </button>
    </div>

    <!-- Khung Bản đồ Leaflet -->
    <div wire:ignore class="loc-map-wrapper">
        <div 
            wire:ignore
            x-ref="mapContainer" 
            id="school-leaflet-map-element"
            class="loc-map-element"
        ></div>

        <!-- Chỉ dẫn nhỏ góc bản đồ -->
        <div class="loc-map-hint">
            💡 Click hoặc kéo thả ghim đỏ để đổi vị trí
        </div>
    </div>

    <!-- Thông tin phản hồi trạng thái & địa chỉ -->
    <div class="loc-status-bar">
        <div style="display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0;">
            <span class="loc-coord-badge">
                Tọa độ: <span style="font-family: monospace; margin-left: 2px;" x-text="currentLat.toFixed(6) + ', ' + currentLng.toFixed(6)"></span>
            </span>
            <div class="loc-status-text">
                <span x-show="isGeocoding" style="color: #d97706; display: inline-flex; align-items: center; gap: 4px;">
                    <svg class="loc-inline-svg loc-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="1"></path>
                    </svg>
                    Đang tìm địa chỉ...
                </span>
                <span x-show="!isGeocoding" x-text="statusMessage"></span>
            </div>
        </div>
    </div>
</div>

<script>
function schoolLocationPicker(config) {
    return {
        map: null,
        marker: null,
        currentLat: config.defaultLat || 20.2506,
        currentLng: config.defaultLng || 105.9745,
        pasteInput: '',
        statusMessage: 'Sẵn sàng chọn vị trí',
        isLocating: false,
        isGeocoding: false,
        fallbackTileAdded: false,

        initComponent() {
            if (this.$wire) {
                const wireLat = this.$wire.get('data.lat');
                const wireLng = this.$wire.get('data.lng');
                if (wireLat && wireLng) {
                    this.currentLat = parseFloat(wireLat);
                    this.currentLng = parseFloat(wireLng);
                }
            }

            this.waitForLeaflet(() => {
                this.initMap();
            });
        },

        waitForLeaflet(callback) {
            if (typeof window.L !== 'undefined') {
                callback();
            } else {
                let attempts = 0;
                const interval = setInterval(() => {
                    attempts++;
                    if (typeof window.L !== 'undefined') {
                        clearInterval(interval);
                        callback();
                    } else if (attempts > 50) {
                        clearInterval(interval);
                        console.error('Không thể tải thư viện Leaflet!');
                    }
                }, 100);
            }
        },

        initMap() {
            if (this.map) return;

            const container = this.$refs.mapContainer || document.getElementById('school-leaflet-map-element');
            if (!container) return;

            if (container._leaflet_id) {
                container._leaflet_id = null;
            }

            this.map = L.map(container, {
                center: [this.currentLat, this.currentLng],
                zoom: 15,
                scrollWheelZoom: true
            });

            // Google Maps Clean Road Tiles (no cluttered POI icons) with OpenStreetMap fallback
            const googleTiles = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&apistyle=s.t:2|p.v:off', {
                maxZoom: 20,
                attribution: '© Google Maps'
            });

            googleTiles.on('tileerror', () => {
                if (!this.fallbackTileAdded) {
                    this.fallbackTileAdded = true;
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(this.map);
                }
            });

            googleTiles.addTo(this.map);

            const icon = L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            this.marker = L.marker([this.currentLat, this.currentLng], {
                draggable: true,
                icon: icon
            }).addTo(this.map);

            this.marker.bindPopup('<b>Vị trí Trường học</b>').openPopup();

            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.setNewCoordinates(pos.lat, pos.lng);
            });

            this.map.on('click', (e) => {
                this.setNewCoordinates(e.latlng.lat, e.latlng.lng);
            });

            setTimeout(() => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            }, 300);

            setTimeout(() => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            }, 1000);
        },

        setNewCoordinates(lat, lng) {
            lat = parseFloat(lat.toFixed(6));
            lng = parseFloat(lng.toFixed(6));

            this.currentLat = lat;
            this.currentLng = lng;

            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
            }
            if (this.map) {
                this.map.panTo([lat, lng]);
            }

            this.syncCoordsToForm(lat, lng);
            this.reverseGeocode(lat, lng);
        },

        syncCoordsToForm(lat, lng) {
            const latInput = document.getElementById('school-lat-input');
            if (latInput) {
                latInput.value = lat;
                latInput.dispatchEvent(new Event('input', { bubbles: true }));
                latInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            const lngInput = document.getElementById('school-lng-input');
            if (lngInput) {
                lngInput.value = lng;
                lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                lngInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (this.$wire) {
                this.$wire.set('data.lat', lat);
                this.$wire.set('data.lng', lng);
            }
        },

        parseCoordinates(input) {
            if (!input || typeof input !== 'string') return null;
            input = input.trim();

            let match = input.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
            if (match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };

            match = input.match(/[?&](?:q|ll)=(-?\d+\.\d+),(-?\d+\.\d+)/);
            if (match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };

            const dmsRegex = /(\d+)[°\s]+(\d+)['\s]+([\d.]+)"?\s*([NS])[\s,]+(\d+)[°\s]+(\d+)['\s]+([\d.]+)"?\s*([EW])/i;
            match = input.match(dmsRegex);
            if (match) {
                let lat = parseFloat(match[1]) + parseFloat(match[2])/60 + parseFloat(match[3])/3600;
                if (match[4].toUpperCase() === 'S') lat = -lat;
                let lng = parseFloat(match[5]) + parseFloat(match[6])/60 + parseFloat(match[7])/3600;
                if (match[8].toUpperCase() === 'W') lng = -lng;
                return { lat: parseFloat(lat.toFixed(6)), lng: parseFloat(lng.toFixed(6)) };
            }

            match = input.match(/(-?\d+(?:\.\d+)?)[,\s;]+(-?\d+(?:\.\d+)?)/);
            if (match) {
                let p1 = parseFloat(match[1]);
                let p2 = parseFloat(match[2]);
                if (Math.abs(p1) <= 90 && Math.abs(p2) <= 180) {
                    return { lat: parseFloat(p1.toFixed(6)), lng: parseFloat(p2.toFixed(6)) };
                } else if (Math.abs(p2) <= 90 && Math.abs(p1) <= 180) {
                    return { lat: parseFloat(p2.toFixed(6)), lng: parseFloat(p1.toFixed(6)) };
                }
            }

            return null;
        },

        applyPastedCoordinates() {
            if (!this.pasteInput) return;
            const coords = this.parseCoordinates(this.pasteInput);
            if (!coords) {
                alert('Không tìm thấy tọa độ hợp lệ! Vui lòng dán theo dạng: 20.2506, 105.9745 hoặc đường link từ Google Maps.');
                return;
            }
            this.setNewCoordinates(coords.lat, coords.lng);
            this.pasteInput = '';
        },

        getCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Trình duyệt của bạn không hỗ trợ định vị GPS!');
                return;
            }
            this.isLocating = true;
            this.statusMessage = 'Đang lấy vị trí GPS hiện tại...';

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.isLocating = false;
                    const lat = parseFloat(pos.coords.latitude.toFixed(6));
                    const lng = parseFloat(pos.coords.longitude.toFixed(6));
                    this.setNewCoordinates(lat, lng);
                },
                (err) => {
                    this.isLocating = false;
                    let msg = 'Không thể lấy vị trí hiện tại.';
                    if (err.code === 1) msg = 'Bạn đã từ chối cấp quyền truy cập vị trí trên trình duyệt.';
                    else if (err.code === 2) msg = 'Không xác định được vị trí thiết bị.';
                    else if (err.code === 3) msg = 'Hết thời gian chờ lấy vị trí.';
                    alert(msg);
                    this.statusMessage = msg;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        },

        async reverseGeocode(lat, lng) {
            this.isGeocoding = true;
            this.statusMessage = 'Đang tra cứu địa chỉ từ vị trí ghim...';

            try {
                let detectedAddress = '';
                let detectedWard = '';

                try {
                    const res = await fetch(`https://photon.komoot.io/reverse?lat=${lat}&lon=${lng}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (data.features && data.features.length > 0) {
                            const props = data.features[0].properties;
                            const parts = [];
                            if (props.name && props.type !== 'city' && props.type !== 'administrative') {
                                parts.push(props.name);
                            }
                            if (props.street && props.street !== props.name) {
                                parts.push(props.street);
                            }
                            if (props.locality) {
                                parts.push(props.locality);
                                detectedWard = props.locality;
                            } else if (props.district) {
                                parts.push(props.district);
                                detectedWard = props.district;
                            }
                            if (props.city && props.city !== props.locality) {
                                parts.push(props.city);
                            }
                            if (props.state) {
                                parts.push(props.state);
                            }
                            detectedAddress = parts.filter(Boolean).join(', ');
                        }
                    }
                } catch (e) {
                    console.warn('Photon reverse geocode failed:', e);
                }

                if (!detectedAddress) {
                    try {
                        const res2 = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=vi`);
                        if (res2.ok) {
                            const data2 = await res2.json();
                            const parts = [];
                            if (data2.locality) parts.push(data2.locality);
                            if (data2.city && data2.city !== data2.locality) parts.push(data2.city);
                            if (data2.principalSubdivision) parts.push(data2.principalSubdivision);
                            detectedAddress = parts.filter(Boolean).join(', ');
                            if (data2.locality) detectedWard = data2.locality;
                        }
                    } catch (e2) {
                        console.warn('Bigdatacloud fallback failed:', e2);
                    }
                }

                if (detectedAddress) {
                    this.statusMessage = `✅ Địa chỉ: ${detectedAddress}`;
                    this.syncAddressToForm(detectedAddress, detectedWard);
                } else {
                    this.statusMessage = `📍 Đã ghim vị trí [${lat}, ${lng}]. Vui lòng nhập địa chỉ chi tiết bên dưới.`;
                }
            } catch (err) {
                console.error(err);
                this.statusMessage = `📍 Đã ghim tọa độ [${lat}, ${lng}]`;
            } finally {
                this.isGeocoding = false;
            }
        },

        syncAddressToForm(address, wardHint) {
            const addrInput = document.getElementById('school-address-input');
            if (addrInput) {
                addrInput.value = address;
                addrInput.dispatchEvent(new Event('input', { bubbles: true }));
                addrInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            if (this.$wire) {
                this.$wire.set('data.address', address);
            }

            if (wardHint) {
                const wardSelect = document.getElementById('school-ward-select') || document.querySelector('select[wire\\:model*="ward"]');
                if (wardSelect) {
                    const cleanHint = wardHint.toLowerCase().replace(/(phường|xã|thị trấn)\s+/g, '').trim();
                    for (let i = 0; i < wardSelect.options.length; i++) {
                        const optText = wardSelect.options[i].text.toLowerCase();
                        const optVal = wardSelect.options[i].value;
                        if (optText.includes(cleanHint) || cleanHint.includes(optText.replace(/(phường|xã|thị trấn)\s+/g, '').trim())) {
                            wardSelect.selectedIndex = i;
                            wardSelect.dispatchEvent(new Event('change', { bubbles: true }));
                            if (this.$wire) {
                                this.$wire.set('data.ward', optVal);
                            }
                            break;
                            }
                    }
                }
            }
        }
    };
}
</script>
