/**
 * GIS Utility Service for Ninh Binh Digital Map
 */

export const NINH_BINH_BOUNDS = [
  [19.90, 105.50], // Southwest
  [20.72, 106.62]  // Northeast
]

export const NINH_BINH_MAX_BOUNDS = [
  [19.78, 105.35], // Southwest limit - cannot pan beyond
  [20.85, 106.75]  // Northeast limit - cannot pan beyond
]

export const NINH_BINH_CENTER = [20.2530, 105.9750]

export const gisService = {
  /**
   * Calculate distance in kilometers between two lat/lng pairs using Haversine formula
   */
  calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371 // Radius of earth in km
    const dLat = this.deg2rad(lat2 - lat1)
    const dLon = this.deg2rad(lon2 - lon1)
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2)
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
    const d = R * c
    return Math.round(d * 100) / 100 // 2 decimal places
  },

  deg2rad(deg) {
    return deg * (Math.PI / 180)
  },

  /**
   * Perform buffer analysis around a center school
   * @param {Object} centerSchool 
   * @param {Array} allSchools 
   * @param {number} radiusKm 
   */
  getBufferAnalysis(centerSchool, allSchools, radiusKm = 3) {
    if (!centerSchool || !centerSchool.lat || !centerSchool.lng) return null

    const neighbors = allSchools
      .filter(s => s.id !== centerSchool.id && s.lat && s.lng)
      .map(s => {
        const distance = this.calculateDistance(centerSchool.lat, centerSchool.lng, s.lat, s.lng)
        return {
          ...s,
          distance
        }
      })
      .filter(s => s.distance <= radiusKm)
      .sort((a, b) => a.distance - b.distance)

    // Summary counts by education level
    const countsByLevel = {}
    neighbors.forEach(s => {
      countsByLevel[s.education_level] = (countsByLevel[s.education_level] || 0) + 1
    })

    return {
      center: centerSchool,
      radiusKm,
      totalCount: neighbors.length,
      neighbors,
      countsByLevel
    }
  },

  /**
   * Convert schools to Heatmap point data: [lat, lng, intensity]
   */
  getHeatmapData(schools) {
    return schools
      .filter(s => s.lat && s.lng)
      .map(s => {
        // Higher weight for schools with larger student population
        const weight = Math.min(1.0, Math.max(0.3, (s.student_count || 500) / 2000))
        return [s.lat, s.lng, weight]
      })
  },

  /**
   * Province Boundary GeoJSON from thanglequoc/vietnamese-provinces-database
   */
  async getProvinceGeoJSON() {
    const data = await import('../data/gis/ninh_binh_province.json')
    return data.default || data
  },

  /**
   * All 129 Wards/Communes GeoJSON from thanglequoc/vietnamese-provinces-database
   */
  async getWardsGeoJSON() {
    const data = await import('../data/gis/ninh_binh_wards.json')
    return data.default || data
  },

  /**
   * Inverted Mask GeoJSON: World polygon with Ninh Binh boundary cut out as a hole.
   * Shading this layer dims everything outside Ninh Binh while leaving Ninh Binh crystal clear.
   */
  async getInvertedMaskGeoJSON() {
    const prov = await this.getProvinceGeoJSON()
    const worldOuterRing = [
      [-180, 90],
      [180, 90],
      [180, -90],
      [-180, -90],
      [-180, 90]
    ]

    const features = []
    if (prov && prov.features && prov.features.length > 0) {
      const geom = prov.features[0].geometry
      if (geom.type === 'MultiPolygon') {
        geom.coordinates.forEach(poly => {
          features.push({
            type: 'Feature',
            properties: { role: 'inverted_mask' },
            geometry: {
              type: 'Polygon',
              coordinates: [worldOuterRing, poly[0]]
            }
          })
        })
      } else if (geom.type === 'Polygon') {
        features.push({
          type: 'Feature',
          properties: { role: 'inverted_mask' },
          geometry: {
            type: 'Polygon',
            coordinates: [worldOuterRing, geom.coordinates[0]]
          }
        })
      }
    }

    return {
      type: 'FeatureCollection',
      features
    }
  }
}
