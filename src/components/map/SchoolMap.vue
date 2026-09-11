<template>
  <div class="relative w-full h-full overflow-hidden">
    <!-- Map Canvas Element -->
    <div id="map" ref="mapContainer" class="w-full h-full z-[1]"></div>

    <!-- Floating Filter Panel Backdrop for Mobile/Tablet -->
    <div 
      v-if="isFilterPanelOpen"
      @click="isFilterPanelOpen = false"
      class="fixed inset-0 bg-slate-900/20 backdrop-blur-2xs z-[999] lg:hidden animate-in fade-in">
    </div>

    <!-- Floating Filter Panel -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform -translate-x-4 opacity-0 scale-95"
      enter-to-class="transform translate-x-0 opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform translate-x-0 opacity-100 scale-100"
      leave-to-class="transform -translate-x-4 opacity-0 scale-95">
      <MapFilterPanel 
        v-if="isFilterPanelOpen"
        :schools="schools"
        :filteredCount="filteredSchools.length"
        :totalCount="schools.length"
        @close="isFilterPanelOpen = false"
      />
    </Transition>

    <!-- Floating GIS Toolbar -->
    <GisToolbar 
      :isFilterPanelOpen="isFilterPanelOpen"
      :activeBasemap="currentBasemap"
      :isWardsBoundaryActive="isWardsBoundaryActive"
      :isBufferActive="isBufferActive"
      :bufferRadius="bufferRadius"
      @toggle-filter-panel="isFilterPanelOpen = !isFilterPanelOpen"
      @change-basemap="changeBasemap"
      @toggle-wards-boundary="toggleWardsBoundary"
      @change-buffer-radius="changeBufferRadius"
      @clear-buffer="clearBuffer"
      @locate-user="locateUser"
      @reset-bounds="resetBounds"
    />

    <!-- Hovered Ward Info Card (Bottom center on mobile, Top center on desktop) -->
    <div 
      v-if="hoveredWard" 
      class="absolute bottom-5 sm:bottom-auto sm:top-3 left-1/2 -translate-x-1/2 z-[1000] glass-panel px-3 py-1.5 rounded-full border border-blue-300 shadow-md text-xs flex items-center gap-1.5 sm:gap-2 animate-in fade-in select-none max-w-[calc(100vw-32px)]">
      <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse flex-shrink-0"></span>
      <span class="font-bold text-slate-800 truncate">{{ hoveredWard.fullName }}</span>
      <span class="text-slate-400 hidden xs:inline">|</span>
      <span class="text-slate-600 hidden xs:inline text-[11px]">Diện tích: <strong class="text-slate-800">{{ hoveredWard.areaKm2 }} km²</strong></span>
    </div>

    <!-- Buffer Analysis Result Banner -->
    <div 
      v-if="isBufferActive && bufferResult" 
      class="absolute bottom-6 right-3 left-3 sm:left-auto z-[1000] glass-panel p-3.5 rounded-2xl border border-purple-200 shadow-xl text-xs sm:max-w-xs animate-in slide-in-from-bottom-2">
      <div class="flex items-center justify-between font-bold text-slate-800 mb-1">
        <span class="flex items-center gap-1.5 text-purple-800">
          <i class="fa-solid fa-bullseye text-purple-600"></i>
          <span>Vùng đệm bán kính {{ bufferRadius }} km</span>
        </span>
        <button @click="clearBuffer" class="text-slate-400 hover:text-slate-600">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <p class="text-[11px] text-slate-600 font-medium truncate mb-2">
        Tâm: <strong class="text-slate-800">{{ bufferResult.center?.name }}</strong>
      </p>

      <div class="bg-purple-50 p-2 rounded-xl text-center mb-2 border border-purple-100">
        <div class="text-lg font-black text-purple-800">{{ bufferResult.totalCount }}</div>
        <div class="text-[10px] text-purple-600 font-semibold">Cơ sở giáo dục trong bán kính</div>
      </div>

      <div class="space-y-1 max-h-36 overflow-y-auto pr-1">
        <div 
          v-for="nb in bufferResult.neighbors" 
          :key="nb.id"
          @click="flyToSchool(nb)"
          class="flex items-center justify-between p-1 rounded-md hover:bg-slate-100 cursor-pointer text-[11px]">
          <span class="truncate flex-1 font-medium text-slate-700">{{ nb.name }}</span>
          <span class="font-bold text-purple-700 font-mono flex-shrink-0 ml-2">{{ nb.distance }} km</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import L from 'leaflet'
import 'leaflet.markercluster'
import 'leaflet.heat'
import MapFilterPanel from './MapFilterPanel.vue'
import GisToolbar from './GisToolbar.vue'
import { LEVEL_MAP } from '../../services/schoolService'
import { gisService, NINH_BINH_CENTER, NINH_BINH_BOUNDS, NINH_BINH_MAX_BOUNDS } from '../../services/gisService'
import { filterStore, targetSchoolToFly } from '../../services/filterStore'

const props = defineProps({
  schools: {
    type: Array,
    default: () => []
  },
  districts: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['view-detail', 'edit-school'])

const mapContainer = ref(null)
let map = null
let markerClusterGroup = null
let markersMap = new Map() // school.id -> L.marker
let baseTileLayer = null
let bufferCircleLayer = null

// Mask & Boundary layers
let maskLayer = null
let provinceBoundaryLayer = null
let wardsBoundaryLayer = null
const isWardsBoundaryActive = ref(true)
const hoveredWard = ref(null)

// Floating panels toggle state (closed by default on mobile & tablet < 1024px)
const isFilterPanelOpen = ref(typeof window !== 'undefined' ? window.innerWidth >= 1024 : true)

// Watch targetSchoolToFly from top search bar
watch(targetSchoolToFly, (school) => {
  if (school) {
    flyToSchool(school)
    targetSchoolToFly.value = null
  }
})

// Selected & Hovered Ward Layer state & styling
let selectedWardLayer = null
let currentHoveredWardLayer = null
let isDirectlyClickingWard = false

function clearHoveredWard() {
  if (currentHoveredWardLayer && currentHoveredWardLayer !== selectedWardLayer) {
    wardsBoundaryLayer?.resetStyle(currentHoveredWardLayer)
    currentHoveredWardLayer = null
  }
  hoveredWard.value = null
}

function highlightSelectedWard(layer) {
  clearHoveredWard()
  if (selectedWardLayer && selectedWardLayer !== layer) {
    wardsBoundaryLayer?.resetStyle(selectedWardLayer)
  }
  selectedWardLayer = layer
  if (layer) {
    layer.setStyle({
      color: '#ea580c', // Distinctive energetic orange border
      weight: 3.5,
      opacity: 1,
      fillColor: '#f97316', // Warm amber-orange fill
      fillOpacity: 0.35,
      dashArray: ''
    })
    layer.bringToFront()
  }
}

function clearWardHighlight() {
  if (selectedWardLayer) {
    wardsBoundaryLayer?.resetStyle(selectedWardLayer)
    selectedWardLayer = null
  }
}

function normalizeWard(str) {
  return (str || '')
    .toLowerCase()
    .replace(/^(xã|phường|thị trấn)\s+/i, '')
    .trim()
}

// Watch ward filter change to automatically focus and highlight selected ward
watch(() => filterStore.ward, (newWard) => {
  if (!map) return

  if (!newWard || newWard === 'all') {
    clearWardHighlight()
    return
  }

  // If this filter change was triggered by directly clicking the polygon on the map, do not re-search
  if (isDirectlyClickingWard) {
    return
  }

  if (wardsBoundaryLayer) {
    let matchedLayer = null
    const target = newWard.toLowerCase().trim()
    const targetNorm = normalizeWard(target)

    // Pass 1: Strict Exact Matching (by code, exact name, or exact normalized name)
    wardsBoundaryLayer.eachLayer(layer => {
      if (matchedLayer) return
      const p = layer.feature?.properties || {}
      const code = String(p.code || '')
      const name = (p.name || '').toLowerCase().trim()
      const fullName = (p.fullName || '').toLowerCase().trim()
      const nameNorm = normalizeWard(name)
      const fullNameNorm = normalizeWard(fullName)

      if (
        (code && code === newWard) ||
        name === target ||
        fullName === target ||
        nameNorm === targetNorm ||
        fullNameNorm === targetNorm
      ) {
        matchedLayer = layer
      }
    })

    // Pass 2: Fallback only if no exact match found
    if (!matchedLayer) {
      wardsBoundaryLayer.eachLayer(layer => {
        if (matchedLayer) return
        const p = layer.feature?.properties || {}
        const nameNorm = normalizeWard(p.name || '')
        const fullNameNorm = normalizeWard(p.fullName || '')

        if (nameNorm.startsWith(targetNorm) || fullNameNorm.startsWith(targetNorm)) {
          matchedLayer = layer
        }
      })
    }

    if (matchedLayer) {
      highlightSelectedWard(matchedLayer)
      map.fitBounds(matchedLayer.getBounds(), { padding: [40, 40], maxZoom: 15 })
      return
    }
  }

  const matched = filteredSchools.value
  if (matched.length > 0 && matched[0].lat && matched[0].lng) {
    map.flyTo([matched[0].lat, matched[0].lng], 14, { duration: 1.2 })
  }
})

// Filtered schools computation from filterStore
const filteredSchools = computed(() => {
  const q = filterStore.search.toLowerCase().trim()
  const filterLvl = (filterStore.level || 'all').replace(/-/g, '_')
  const selectedW = filterStore.ward && filterStore.ward !== 'all'
    ? filterStore.ward.toLowerCase().trim()
    : ''
  const selectedWNorm = selectedW ? normalizeWard(selectedW) : ''
  const wardAddressRegex = selectedWNorm
    ? new RegExp(`(^|[,\\s])(xã|phường|thị trấn)\\s+${selectedWNorm}([,\\s]|$)`, 'i')
    : null

  return props.schools.filter(s => {
    const matchSearch = !q || 
      (s.name || '').toLowerCase().includes(q) ||
      (s.code || '').toLowerCase().includes(q) ||
      (s.address || '').toLowerCase().includes(q) ||
      (s.ward || '').toLowerCase().includes(q)

    const sLevel = (s.education_level || '').replace(/-/g, '_')
    const matchLevel = filterLvl === 'all' || sLevel === filterLvl

    let matchWard = true
    if (selectedWNorm) {
      const sWard = (s.ward || '').toLowerCase().trim()
      const sWardNorm = normalizeWard(sWard)

      if (sWardNorm && selectedWNorm) {
        matchWard = sWardNorm === selectedWNorm
      } else if (!sWard && s.address) {
        const sAddr = (s.address || '').toLowerCase().trim()
        matchWard = wardAddressRegex?.test(sAddr) || false
      } else {
        matchWard = false
      }
    }

    return matchSearch && matchLevel && matchWard
  })
})

// GIS tools state
const currentBasemap = ref('google_clean')
const isBufferActive = ref(false)
const bufferRadius = ref(3)
const bufferCenterSchool = ref(null)
const bufferResult = ref(null)

// Tile layer URLs (Free open tiles, no API key required)
const TILE_PROVIDERS = {
  google_clean: {
    url: 'https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&apistyle=s.t:2|p.v:off',
    attribution: '&copy; Google Maps (Tối giản - Không icon)'
  },
  google_streets: {
    url: 'https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
    attribution: '&copy; Google Maps (Bản đồ đường phố)'
  },
  satellite: {
    url: 'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
    attribution: '&copy; Google (Ảnh vệ tinh)'
  }
}

async function initMap() {
  if (!mapContainer.value) return

  // Initialize map centered at Ninh Binh with strict boundary constraints
  map = L.map(mapContainer.value, {
    center: NINH_BINH_CENTER,
    zoom: 11,
    minZoom: 10,
    maxZoom: 19,
    maxBounds: NINH_BINH_MAX_BOUNDS,
    maxBoundsViscosity: 1.0, // Prevent dragging beyond Ninh Binh limits
    zoomControl: false
  })

  // Create custom pane for the inverted mask outside Ninh Binh
  // Sits between base tiles (zIndex 200) and overlay vectors/markers (zIndex 400+)
  const maskPane = map.createPane('maskPane')
  maskPane.style.zIndex = '350'
  maskPane.style.pointerEvents = 'none'

  // Add zoom control at bottom-right
  L.control.zoom({ position: 'bottomright' }).addTo(map)

  // Add initial base tile layer (Google clean without cluttered icons)
  setTileLayer('google_clean')

  // Load Administrative Boundaries and Inverted Dimming Mask
  await loadAdministrativeBoundaries()

  // Initialize Marker Cluster Group with custom pie-chart cluster icons
  markerClusterGroup = L.markerClusterGroup({
    chunkedLoading: true,
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    zoomToBoundsOnClick: true,
    maxClusterRadius: 40,
    iconCreateFunction: createPieClusterIcon
  })
  map.addLayer(markerClusterGroup)

  // Clean up hovered ward styling when cursor leaves the map container or clicks background
  map.on('mouseout', () => {
    clearHoveredWard()
  })
  map.on('click', () => {
    clearHoveredWard()
  })

  // Render initial school markers
  renderMarkers(filteredSchools.value)

  nextTick(() => {
    map.invalidateSize()
  })
}

/**
 * Load Province and Wards boundaries from thanglequoc/vietnamese-provinces-database
 */
async function loadAdministrativeBoundaries() {
  try {
    // 0. Inverted Mask Layer (Dims outside Ninh Binh while keeping Ninh Binh clear)
    const maskGeoJSON = await gisService.getInvertedMaskGeoJSON()
    maskLayer = L.geoJSON(maskGeoJSON, {
      pane: 'maskPane',
      style: {
        fillColor: currentBasemap.value === 'satellite' ? '#0b0f19' : '#f1f5f9',
        fillOpacity: currentBasemap.value === 'satellite' ? 0.78 : 0.72,
        stroke: false
      },
      interactive: false
    }).addTo(map)

    // 1. Province Boundary Layer (Removed blue border around Ninh Binh as requested)

    // 2. All 129 Wards/Communes Boundary Layer
    const wardsGeoJSON = await gisService.getWardsGeoJSON()
    wardsBoundaryLayer = L.geoJSON(wardsGeoJSON, {
      style: (feature) => ({
        color: '#4f46e5',
        weight: 1.2,
        opacity: 0.65,
        fillColor: '#6366f1',
        fillOpacity: 0.05,
        dashArray: '2, 3'
      }),
      onEachFeature: (feature, layer) => {
        const wardProps = feature.properties || {}
        
        // Hover & click interactions
        layer.on({
          mouseover: (e) => {
            const l = e.target

            // Ensure any previous hovered layer is cleanly reset
            if (currentHoveredWardLayer && currentHoveredWardLayer !== l && currentHoveredWardLayer !== selectedWardLayer) {
              wardsBoundaryLayer?.resetStyle(currentHoveredWardLayer)
            }
            currentHoveredWardLayer = l

            // Highlight current layer if not selected (do NOT call bringToFront to prevent dropped mouseout events)
            if (l !== selectedWardLayer) {
              l.setStyle({
                weight: 2.2,
                color: '#2563eb',
                fillColor: '#93c5fd',
                fillOpacity: 0.28,
                dashArray: ''
              })
            }
            hoveredWard.value = {
              fullName: wardProps.fullName || wardProps.name,
              areaKm2: wardProps.areaKm2 || '—'
            }
          },
          mouseout: (e) => {
            const l = e.target
            // Do NOT reset style if this is the active selected ward!
            if (l !== selectedWardLayer) {
              wardsBoundaryLayer?.resetStyle(l)
            }
            if (currentHoveredWardLayer === l) {
              currentHoveredWardLayer = null
            }
            hoveredWard.value = null
          },
          click: (e) => {
            L.DomEvent.stopPropagation(e)

            // 1. Highlight the selected ward with distinctive amber-orange color tone!
            highlightSelectedWard(layer)

            // 2. Sync to filterStore with direct click flag to prevent watcher re-matching
            const wardName = wardProps.name || ''
            const wardFullName = wardProps.fullName || `${wardProps.unit_type || 'Xã'} ${wardProps.name || ''}`
            
            isDirectlyClickingWard = true
            filterStore.ward = wardName
            nextTick(() => {
              isDirectlyClickingWard = false
            })

            map.fitBounds(layer.getBounds(), { padding: [40, 40], maxZoom: 15 })

            // Count schools in this ward
            const schoolsInWard = findSchoolsInWard(wardName, wardFullName)

            const popupContent = `
              <div class="p-3 select-none">
                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full inline-block mb-1">
                  Đơn vị hành chính cấp Xã/Phường
                </div>
                <h4 class="font-bold text-slate-800 text-sm mb-1">${wardFullName}</h4>
                <p class="text-xs text-slate-500 mb-2">
                  <span>Diện tích: <strong>${wardProps.areaKm2 || '—'} km²</strong></span>
                </p>
                <div class="border-t border-slate-100 pt-2">
                  <div class="flex items-center justify-between text-xs font-semibold text-slate-700 mb-1.5">
                    <span>Cơ sở giáo dục trong địa bàn:</span>
                    <span class="text-blue-800 font-bold bg-blue-50 px-2 py-0.5 rounded">${schoolsInWard.length} trường</span>
                  </div>
                  ${schoolsInWard.length > 0 ? `
                    <div class="space-y-1.5 max-h-36 overflow-y-auto mt-1 pr-1">
                      ${schoolsInWard.map(s => `
                        <div 
                          onclick="window.__openSchoolFromWardPopup && window.__openSchoolFromWardPopup('${s.id}')"
                          class="text-[11px] p-1.5 rounded-lg bg-slate-50 hover:bg-blue-50 border border-slate-200/70 hover:border-blue-300 text-slate-700 hover:text-blue-700 flex items-center justify-between cursor-pointer transition-colors group">
                          <div class="flex items-center gap-1.5 min-w-0 pr-1">
                            <i class="fa-solid fa-graduation-cap text-[10px] text-blue-500 group-hover:scale-110 transition-transform"></i>
                            <span class="truncate font-medium">${s.name}</span>
                          </div>
                          <span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-100/80 text-blue-800 font-semibold uppercase flex-shrink-0">${s.education_level_name || s.education_level}</span>
                        </div>
                      `).join('')}
                    </div>
                  ` : `<p class="text-[11px] text-slate-400 italic">Chưa có cơ sở giáo dục trong danh mục</p>`}
                </div>
              </div>
            `
            layer.bindPopup(popupContent, { maxWidth: 300 }).openPopup()
          }
        })
      }
    })

    if (isWardsBoundaryActive.value) {
      wardsBoundaryLayer.addTo(map)
    }

  } catch (err) {
    console.error('Error loading boundaries:', err)
  }
}

function findSchoolsInWard(wardName, wardFullName) {
  const targetNorm = normalizeWard(wardName || wardFullName)
  if (!targetNorm) return []

  return (props.schools || []).filter(s => {
    const sWardNorm = normalizeWard(s.ward)
    if (sWardNorm) {
      return sWardNorm === targetNorm
    }
    if (!s.ward && s.address) {
      const sAddr = (s.address || '').toLowerCase().trim()
      const regex = new RegExp(`(^|[,\\s])(xã|phường|thị trấn)\\s+${targetNorm}([,\\s]|$)`, 'i')
      return regex.test(sAddr)
    }
    return false
  })
}


function toggleWardsBoundary() {
  isWardsBoundaryActive.value = !isWardsBoundaryActive.value
  if (!map || !wardsBoundaryLayer) return

  if (isWardsBoundaryActive.value) {
    wardsBoundaryLayer.addTo(map)
  } else {
    map.removeLayer(wardsBoundaryLayer)
  }
}

function setTileLayer(providerKey) {
  if (baseTileLayer) {
    map.removeLayer(baseTileLayer)
  }
  const provider = TILE_PROVIDERS[providerKey] || TILE_PROVIDERS.google_streets
  baseTileLayer = L.tileLayer(provider.url, {
    attribution: provider.attribution,
    maxZoom: 19
  }).addTo(map)
}

function changeBasemap(type) {
  currentBasemap.value = type
  setTileLayer(type)
  if (maskLayer) {
    maskLayer.setStyle({
      fillColor: type === 'satellite' ? '#0b0f19' : '#f1f5f9',
      fillOpacity: type === 'satellite' ? 0.78 : 0.72
    })
  }
}

function renderMarkers(schools) {
  if (!markerClusterGroup) return

  const wantedIds = new Set(schools.map(s => s.id))
  const availableIds = new Set((props.schools || []).map(s => s.id))

  // Keep existing markers and only change the filtered delta. This avoids
  // rebuilding all Leaflet DOM nodes and popup handlers on every filter input.
  for (const [id, marker] of markersMap) {
    if (!wantedIds.has(id)) {
      markerClusterGroup.removeLayer(marker)
    }
    if (!availableIds.has(id)) {
      markersMap.delete(id)
    }
  }

  schools.forEach(school => {
    if (!school.lat || !school.lng) return

    const existingMarker = markersMap.get(school.id)
    if (existingMarker) {
      // A CRUD refresh replaces school objects. Recreate only changed markers
      // so updated coordinates/popup content are reflected without rebuilding
      // markers that are merely being filtered.
      if (existingMarker.schoolData !== school) {
        markerClusterGroup.removeLayer(existingMarker)
        markersMap.delete(school.id)
      } else {
        if (!markerClusterGroup.hasLayer(existingMarker)) {
          markerClusterGroup.addLayer(existingMarker)
        }
        return
      }
    }

    const levelConfig = LEVEL_MAP[school.education_level] || { color: '#64748b', icon: 'fa-solid fa-school' }
    
    // Custom HTML Upright Pin Marker (Google Maps style)
    const iconHtml = `
      <div class="edu-marker-pin" id="marker-${school.id}">
        <div class="edu-marker-head" style="background-color: ${levelConfig.color};">
          <i class="${levelConfig.icon} edu-marker-icon"></i>
        </div>
        <div class="edu-marker-tip" style="border-top-color: ${levelConfig.color};"></div>
      </div>
    `

    const customIcon = L.divIcon({
      className: 'edu-marker-container',
      html: iconHtml,
      iconSize: [32, 38],
      iconAnchor: [16, 36],
      popupAnchor: [0, -38]
    })

    const marker = L.marker([school.lat, school.lng], { icon: customIcon })
    marker.schoolData = school
    
    // Direct click on marker opens the right-hand detail drawer
    marker.on('click', () => {
      emit('view-detail', school)
    })

    // Bind rich popup matching reference layout
    marker.bindPopup(buildPopupHtml(school), {
      maxWidth: 320,
      minWidth: 260
    })

    // Add popup event listeners for action buttons
    marker.on('popupopen', () => {
      setupPopupEventListeners(school)
    })

    markerClusterGroup.addLayer(marker)
    markersMap.set(school.id, marker)
  })
}

/**
 * Create custom pie-chart cluster icon with color slices proportional to education levels
 */
function createPieClusterIcon(cluster) {
  const markers = cluster.getAllChildMarkers()
  const total = markers.length

  // Count schools per education level
  const counts = {}
  markers.forEach(m => {
    const level = m.schoolData?.education_level || 'gdtx'
    counts[level] = (counts[level] || 0) + 1
  })

  // Sizing based on total count
  let size = 38
  let haloSize = 50
  let fontSize = 13
  if (total >= 10) {
    size = 44
    haloSize = 58
    fontSize = 14
  }
  if (total >= 50) {
    size = 50
    haloSize = 66
    fontSize = 15
  }

  // Generate proportional conic-gradient
  const gradient = buildPieGradient(counts, total)

  // Build informative tooltip text
  const tooltipLines = Object.entries(counts)
    .sort((a, b) => b[1] - a[1])
    .map(([lvl, cnt]) => {
      const label = LEVEL_MAP[lvl]?.label || lvl
      const pct = Math.round((cnt / total) * 100)
      return `${cnt} ${label} (${pct}%)`
    })
  const tooltipTitle = `${total} cơ sở giáo dục:\n• ${tooltipLines.join('\n• ')}`

  const html = `
    <div class="edu-pie-cluster" style="width: ${haloSize}px; height: ${haloSize}px;" title="${tooltipTitle}">
      <div class="edu-pie-cluster-halo" style="width: ${haloSize}px; height: ${haloSize}px;"></div>
      <div class="edu-pie-cluster-pie" style="width: ${size}px; height: ${size}px; background: ${gradient};">
        <div class="edu-pie-cluster-badge">
          <span class="edu-pie-cluster-num" style="font-size: ${fontSize}px;">${total}</span>
        </div>
      </div>
    </div>
  `

  return L.divIcon({
    html: html,
    className: 'edu-cluster-div-icon',
    iconSize: L.point(haloSize, haloSize),
    iconAnchor: L.point(haloSize / 2, haloSize / 2)
  })
}

function buildPieGradient(counts, total) {
  const entries = Object.entries(counts)
    .filter(([_, cnt]) => cnt > 0)
    .sort((a, b) => b[1] - a[1]) // largest share first

  if (entries.length === 0) {
    return '#1d4ed8'
  }

  if (entries.length === 1) {
    const level = entries[0][0]
    return LEVEL_MAP[level]?.color || '#1d4ed8'
  }

  let accumulated = 0
  const stops = []

  entries.forEach(([level, count], index) => {
    const color = LEVEL_MAP[level]?.color || '#64748b'
    const percent = (count / total) * 100
    const start = accumulated
    const end = index === entries.length - 1 ? 100 : accumulated + percent
    accumulated = end

    stops.push(`${color} ${start.toFixed(1)}% ${end.toFixed(1)}%`)
  })

  return `conic-gradient(${stops.join(', ')})`
}

function buildPopupHtml(s) {
  const levelConfig = LEVEL_MAP[s.education_level] || { label: s.education_level, color: '#3b82f6' }
  const students = (s.student_count || 0).toLocaleString('vi-VN')
  const teachers = (s.teacher_count || 0)
  const classes = (s.class_count || 0)
  const typeLabel = s.school_type === 'cong_lap' ? 'Công lập' : 'Tư thục'

  return `
    <div class="p-3.5 select-none">
      <!-- Tag Badges -->
      <div class="flex items-center gap-1.5 mb-2">
        <span style="background-color: ${levelConfig.color}; color: white;" class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
          ${levelConfig.label}
        </span>
      </div>

      <!-- Title -->
      <h3 class="font-bold text-slate-800 text-sm leading-snug mb-1">
        ${s.name}
      </h3>

      <!-- Address -->
      <p class="text-[11px] text-slate-500 flex items-start gap-1.5 mb-1">
        <i class="fa-solid fa-location-dot text-slate-400 mt-0.5"></i>
        <span>${s.address || `${s.ward}, ${s.district_name}`}</span>
      </p>

      <!-- Principal -->
      ${s.principal ? `
        <p class="text-[11px] text-slate-500 flex items-center gap-1.5 mb-2">
          <i class="fa-solid fa-user-tie text-slate-400"></i>
          <span>HT: ${s.principal}</span>
        </p>
      ` : ''}

      <!-- Stats Grid -->
      <div class="grid grid-cols-3 gap-1.5 my-2.5 bg-slate-50 p-2 rounded-xl border border-slate-100 text-center">
        <div class="p-1">
          <div class="text-xs font-bold text-blue-700 font-mono">${students}</div>
          <div class="text-[10px] text-slate-400">Học sinh</div>
        </div>
        <div class="p-1 border-x border-slate-200/80">
          <div class="text-xs font-bold text-emerald-700 font-mono">${teachers}</div>
          <div class="text-[10px] text-slate-400">Giáo viên</div>
        </div>
        <div class="p-1">
          <div class="text-xs font-bold text-purple-700 font-mono">${classes}</div>
          <div class="text-[10px] text-slate-400">Lớp học</div>
        </div>
      </div>

      <!-- Actions -->
      <div class="grid grid-cols-2 gap-1.5 pt-1">
        <button id="btn-popup-detail-${s.id}" class="w-full py-1.5 px-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold flex items-center justify-center gap-1 transition-colors">
          <i class="fa-solid fa-circle-info text-[11px]"></i>
          <span>Chi tiết hồ sơ</span>
        </button>
        <a href="https://www.google.com/maps/dir/?api=1&destination=${s.lat},${s.lng}" target="_blank" class="w-full py-1.5 px-2 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold flex items-center justify-center gap-1 transition-colors text-decoration-none">
          <i class="fa-solid fa-diamond-turn-right text-[11px]"></i>
          <span>Chỉ đường</span>
        </a>
      </div>

      <div class="flex gap-1.5 mt-1.5">
        <button id="btn-popup-buffer-${s.id}" class="flex-1 py-1 rounded-md bg-purple-50 text-purple-700 hover:bg-purple-100 text-[11px] font-medium flex items-center justify-center gap-1 transition-colors">
          <i class="fa-solid fa-bullseye text-[10px]"></i>
          <span>Bán kính 3km</span>
        </button>
        <button id="btn-popup-edit-${s.id}" class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 hover:bg-slate-200 text-[11px] font-medium transition-colors">
          <i class="fa-solid fa-pen text-[10px]"></i>
        </button>
      </div>
    </div>
  `
}

function setupPopupEventListeners(school) {
  setTimeout(() => {
    const btnDetail = document.getElementById(`btn-popup-detail-${school.id}`)
    if (btnDetail) {
      btnDetail.onclick = (e) => {
        e.preventDefault()
        emit('view-detail', school)
      }
    }

    const btnBuffer = document.getElementById(`btn-popup-buffer-${school.id}`)
    if (btnBuffer) {
      btnBuffer.onclick = (e) => {
        e.preventDefault()
        triggerBufferForSchool(school)
      }
    }

    const btnEdit = document.getElementById(`btn-popup-edit-${school.id}`)
    if (btnEdit) {
      btnEdit.onclick = (e) => {
        e.preventDefault()
        emit('edit-school', school)
      }
    }
  }, 50)
}

function flyToSchool(school, openDetail = true) {
  if (!school.lat || !school.lng || !map) return
  
  if (openDetail) {
    emit('view-detail', school)
  }

  map.flyTo([school.lat, school.lng], 16, {
    duration: 1.2
  })

  setTimeout(() => {
    const marker = markersMap.get(school.id)
    if (marker) {
      markerClusterGroup.zoomToShowLayer(marker, () => {
        marker.openPopup()
      })
    }
  }, 800)
}

// GIS Feature: Buffer Analysis
function triggerBufferForSchool(school, radius = bufferRadius.value) {
  bufferCenterSchool.value = school
  bufferRadius.value = radius
  isBufferActive.value = true

  const result = gisService.getBufferAnalysis(school, props.schools, radius)
  bufferResult.value = result

  // Draw buffer circle on map
  if (bufferCircleLayer) {
    map.removeLayer(bufferCircleLayer)
  }

  bufferCircleLayer = L.circle([school.lat, school.lng], {
    radius: radius * 1000,
    color: '#9333ea',
    weight: 2,
    opacity: 0.8,
    fillColor: '#a855f7',
    fillOpacity: 0.12,
    dashArray: '6, 6'
  }).addTo(map)

  map.fitBounds(bufferCircleLayer.getBounds(), { padding: [50, 50] })
}

function changeBufferRadius(radius) {
  bufferRadius.value = radius
  if (bufferCenterSchool.value) {
    triggerBufferForSchool(bufferCenterSchool.value, radius)
  } else if (props.schools.length > 0) {
    triggerBufferForSchool(props.schools[0], radius)
  }
}

function clearBuffer() {
  isBufferActive.value = false
  bufferCenterSchool.value = null
  bufferResult.value = null
  if (bufferCircleLayer) {
    map.removeLayer(bufferCircleLayer)
    bufferCircleLayer = null
  }
}

// GIS Feature: Locate User
function locateUser() {
  if (!navigator.geolocation) {
    alert('Trình duyệt không hỗ trợ định vị GPS.')
    return
  }
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = pos.coords.latitude
      const lng = pos.coords.longitude
      
      const userMarker = L.circleMarker([lat, lng], {
        radius: 8,
        color: '#2563eb',
        fillColor: '#3b82f6',
        fillOpacity: 1,
        weight: 3
      }).addTo(map)
      userMarker.bindPopup('<b>Vị trí của bạn</b>').openPopup()

      map.flyTo([lat, lng], 14)
    },
    () => {
      map.flyTo(NINH_BINH_CENTER, 14)
    }
  )
}

function resetBounds() {
  if (!map) return
  map.flyToBounds(NINH_BINH_BOUNDS, {
    padding: [20, 20],
    duration: 1.2
  })
}

// Watch filtered schools change to re-render markers
watch(filteredSchools, (newSchools) => {
  renderMarkers(newSchools)
})

onMounted(() => {
  window.__openSchoolFromWardPopup = (schoolId) => {
    const s = props.schools.find(item => item.id === schoolId)
    if (s) {
      emit('view-detail', s)
    }
  }
  initMap()
})

onUnmounted(() => {
  delete window.__openSchoolFromWardPopup
  if (map) {
    map.remove()
    map = null
  }
})

defineExpose({
  flyToSchool,
  triggerBufferForSchool,
  resetBounds
})
</script>
