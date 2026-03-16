import * as PIXI from 'pixi.js';

export const AssetManager = {
    textures: {},

    async init(app) {
        // If textures are already loaded, don't reload to avoid context loss issues and redundancy
        if (this.textures.soil) return;

        // Create textures from SVGs using Assets API (PixiJS v8)
        // We use data URIs to avoid Blob URL issues with strict CSP or revocations
        this.textures.soil = await this.loadSvgTexture('soil', this.getSoilSvg());
        this.textures.water = await this.loadSvgTexture('water', this.getWaterSvg());
        this.textures.plant = await this.loadSvgTexture('plant', this.getPlantSvg());
        this.textures.robot_basic = await this.loadSvgTexture('robot_basic', this.getRobotSvg());
        this.textures.robot_scout = await this.loadSvgTexture('robot_scout', this.getScoutSvg());
        this.textures.robot_harvester = await this.loadSvgTexture('robot_harvester', this.getHarvesterSvg());
        this.textures.rock = await this.loadSvgTexture('rock', this.getRockSvg());
        
        // Default alias
        this.textures.robot = this.textures.robot_basic;
    },

    async loadSvgTexture(alias, svgString) {
        // Convert SVG string to Data URI
        const svgBase64 = btoa(unescape(encodeURIComponent(svgString)));
        const dataUri = `data:image/svg+xml;base64,${svgBase64}`;
        
        // Use Assets API to register and load the texture
        // We use add + load pattern which is standard in PixiJS v7/v8
        // This avoids manual Image creation and deprecated loadParser usage
        
        // Check if already exists in Assets cache to avoid "already exists" warning/error
        if (!PIXI.Assets.cache.has(alias)) {
            PIXI.Assets.add({ alias, src: dataUri });
        }

        try {
            const texture = await PIXI.Assets.load(alias);
            
            // Ensure texture settings for pixel art / crisp rendering
            if (texture.source) {
                texture.source.scaleMode = 'nearest';
            }
            
            return texture;
        } catch (error) {
            console.error(`Error loading texture ${alias}:`, error);
            // Fallback: Create texture from Image directly if Assets API fails
            const image = new Image();
            image.src = dataUri;
            await new Promise((resolve, reject) => {
                image.onload = resolve;
                image.onerror = reject;
            });
            return PIXI.Texture.from(image);
        }
    },

    getSoilSvg() {
        // Futuristic Farm Tile: Dark Slate with subtle tech grid and glow
        return `<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="soilGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#1e293b;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#0f172a;stop-opacity:1" />
                </linearGradient>
                <pattern id="grid" width="16" height="16" patternUnits="userSpaceOnUse">
                    <path d="M 16 0 L 0 0 0 16" fill="none" stroke="#334155" stroke-width="0.5" opacity="0.3"/>
                </pattern>
            </defs>
            <rect width="64" height="64" fill="url(#soilGrad)"/>
            <rect width="64" height="64" fill="url(#grid)"/>
            <!-- Tech Corners -->
            <path d="M2 2h4 M2 2v4 M62 2h-4 M62 2v4 M2 62h4 M2 62v-4 M62 62h-4 M62 62v-4" 
                  stroke="#475569" stroke-width="2" fill="none" opacity="0.5"/>
            <!-- Center Node -->
            <circle cx="32" cy="32" r="1.5" fill="#334155" opacity="0.5"/>
        </svg>`;
    },

    getWaterSvg() {
        // Cyber Water: Animated-looking gradient
        return `<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="waterGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" style="stop-color:#0ea5e9;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#0284c7;stop-opacity:1" />
                </linearGradient>
                <filter id="glow">
                    <feGaussianBlur stdDeviation="1.5" result="coloredBlur"/>
                    <feMerge>
                        <feMergeNode in="coloredBlur"/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>
            </defs>
            <rect width="64" height="64" fill="url(#waterGrad)"/>
            <!-- Waves/Data Lines -->
            <path d="M0 20 Q16 15 32 20 T64 20 M0 40 Q16 45 32 40 T64 40" 
                  stroke="#7dd3fc" stroke-width="1.5" fill="none" opacity="0.4"/>
            <circle cx="16" cy="16" r="2" fill="#bae6fd" opacity="0.6"/>
            <circle cx="48" cy="50" r="3" fill="#bae6fd" opacity="0.4"/>
        </svg>`;
    },

    getPlantSvg() {
        // Neon Plant: Glowing organic-tech hybrid
        return `<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <radialGradient id="plantGlow" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
                    <stop offset="0%" style="stop-color:#4ade80;stop-opacity:0.8" />
                    <stop offset="100%" style="stop-color:#4ade80;stop-opacity:0" />
                </radialGradient>
            </defs>
            <!-- Glow background -->
            <circle cx="32" cy="40" r="20" fill="url(#plantGlow)" opacity="0.4"/>
            <!-- Stem -->
            <path d="M32 54 Q32 40 32 25" stroke="#22c55e" stroke-width="3" fill="none" stroke-linecap="round"/>
            <!-- Leaves -->
            <path d="M32 40 Q15 35 15 25 Q20 20 32 35" fill="#4ade80" stroke="#16a34a" stroke-width="1"/>
            <path d="M32 35 Q49 30 49 20 Q44 15 32 30" fill="#4ade80" stroke="#16a34a" stroke-width="1"/>
            <!-- Tech Bud -->
            <circle cx="32" cy="20" r="6" fill="#86efac" stroke="#166534" stroke-width="1"/>
            <circle cx="32" cy="20" r="2" fill="#fff"/>
        </svg>`;
    },

    getRobotSvg() {
        // Drone Bot: High-tech look
        return `<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <filter id="droneShadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feGaussianBlur in="SourceAlpha" stdDeviation="2"/>
                    <feOffset dx="0" dy="4" result="offsetblur"/>
                    <feComponentTransfer>
                        <feFuncA type="linear" slope="0.5"/>
                    </feComponentTransfer>
                    <feMerge>
                        <feMergeNode/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>
            </defs>
            <g filter="url(#droneShadow)">
                <!-- Body -->
                <rect x="20" y="20" width="24" height="24" rx="6" fill="#e2e8f0" stroke="#475569" stroke-width="2"/>
                <!-- Eye/Sensor -->
                <rect x="24" y="26" width="16" height="8" rx="2" fill="#0f172a"/>
                <circle cx="32" cy="30" r="2" fill="#ef4444" class="animate-pulse">
                    <animate attributeName="opacity" values="1;0.5;1" dur="2s" repeatCount="indefinite"/>
                </circle>
                <!-- Rotors -->
                <circle cx="16" cy="16" r="8" fill="#94a3b8" opacity="0.5"/>
                <circle cx="48" cy="16" r="8" fill="#94a3b8" opacity="0.5"/>
                <circle cx="16" cy="48" r="8" fill="#94a3b8" opacity="0.5"/>
                <circle cx="48" cy="48" r="8" fill="#94a3b8" opacity="0.5"/>
                <!-- Arms -->
                <line x1="22" y1="22" x2="16" y2="16" stroke="#64748b" stroke-width="2"/>
                <line x1="42" y1="22" x2="48" y2="16" stroke="#64748b" stroke-width="2"/>
                <line x1="22" y1="42" x2="16" y2="48" stroke="#64748b" stroke-width="2"/>
                <line x1="42" y1="42" x2="48" y2="48" stroke="#64748b" stroke-width="2"/>
            </g>
        </svg>`;
    },

    getScoutSvg() {
        // Scout Unit: Yellow/Fast
        return `<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="32" cy="56" rx="16" ry="4" fill="#000" opacity="0.3"/>
            <path d="M32 14 L50 46 H14 Z" fill="#fef08a" stroke="#eab308" stroke-width="2" stroke-linejoin="round"/>
            <rect x="26" y="28" width="12" height="8" rx="2" fill="#1e293b"/>
            <circle cx="32" cy="32" r="3" fill="#06b6d4"/>
            <!-- Wings -->
            <path d="M14 36 L4 30 L14 24" fill="#fef9c3" opacity="0.8"/>
            <path d="M50 36 L60 30 L50 24" fill="#fef9c3" opacity="0.8"/>
        </svg>`;
    },

    getHarvesterSvg() {
        // Harvester Unit: Orange/Heavy
        return `<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="32" cy="56" rx="20" ry="5" fill="#000" opacity="0.3"/>
            <rect x="14" y="20" width="36" height="26" rx="6" fill="#fdba74" stroke="#f97316" stroke-width="2"/>
            <!-- Face -->
            <rect x="18" y="24" width="28" height="10" rx="2" fill="#3f2c22"/>
            <rect x="22" y="27" width="8" height="4" fill="#ef4444"/>
            <rect x="34" y="27" width="8" height="4" fill="#ef4444"/>
            <!-- Treads -->
            <path d="M10 44 H54 L52 52 H12 Z" fill="#4b5563"/>
            <circle cx="16" cy="48" r="3" fill="#1f2937"/>
            <circle cx="26" cy="48" r="3" fill="#1f2937"/>
            <circle cx="36" cy="48" r="3" fill="#1f2937"/>
            <circle cx="48" cy="48" r="3" fill="#1f2937"/>
        </svg>`;
    },

    getRockSvg() {
        // Crystalline Obstacle
        return `<svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="rockGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#475569;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#1e293b;stop-opacity:1" />
                </linearGradient>
            </defs>
            <path d="M32 54 L12 42 L18 22 L32 10 L46 22 L52 42 Z" fill="url(#rockGrad)" stroke="#64748b" stroke-width="2" stroke-linejoin="round"/>
            <path d="M32 54 L32 28 M12 42 L32 28 M52 42 L32 28 M18 22 L32 28 M46 22 L32 28 M32 10 L32 28" 
                  stroke="#94a3b8" stroke-width="1" opacity="0.5"/>
            <!-- Highlights -->
            <path d="M32 10 L18 22 L32 28 Z" fill="#fff" opacity="0.1"/>
            <path d="M46 22 L52 42 L32 54 L32 28 Z" fill="#000" opacity="0.2"/>
        </svg>`;
    }
};
