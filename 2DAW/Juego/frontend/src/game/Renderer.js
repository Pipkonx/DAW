import * as PIXI from 'pixi.js';
import { gsap } from 'gsap';
import { TILE_TYPES } from './Constants';
import { AssetManager } from './Assets';

export class GameRenderer {
    constructor() {
        this.app = null;
        this.tileSize = 64; // Increased for better detail
        this.gridContainer = new PIXI.Container();
        this.robotSprite = null;
        this.tileSprites = []; // 2D array to store sprite references
        this.width = 0;
        this.height = 0;
    }

    destroy() {
        if (this.app) {
            gsap.killTweensOf(this.robotSprite);
            gsap.killTweensOf(this.robotSprite.scale);
            gsap.killTweensOf(this.robotContainer);
            this.app.destroy(true, { children: true, texture: false, baseTexture: false });
            this.app = null;
        }
    }

    async init(containerElement, width, height) {
        this.destroy(); // Limpiar instancia previa si existe

        this.width = width;
        this.height = height;
        
        this.app = new PIXI.Application();
        await this.app.init({ 
            width: width * this.tileSize, 
            height: height * this.tileSize, 
            background: 0x020617, // Slate 950 (Deep Space/Night)
            resolution: window.devicePixelRatio || 1,
            autoDensity: true,
            antialias: true, // Smoother edges
            powerPreference: 'high-performance',
            preserveDrawingBuffer: true // Ayuda a evitar la pérdida del contexto en algunos navegadores
        });

        // Manejo de pérdida de contexto WebGL
        this.app.renderer.on('context', (context) => {
            console.log("WebGL Context event:", context);
        });
        
        // Load Assets
        await AssetManager.init(this.app);

        // Pre-upload textures to GPU to avoid lazy init warning
        this.uploadTextures();

        containerElement.appendChild(this.app.canvas);
        
        // Layers
        this.gridContainer = new PIXI.Container();
        this.gridOverlay = new PIXI.Graphics(); // Grid lines
        this.objectContainer = new PIXI.Container(); // Objects like plants/rocks
        this.robotContainer = new PIXI.Container();
        
        this.app.stage.addChild(this.gridContainer);
        this.app.stage.addChild(this.gridOverlay);
        this.app.stage.addChild(this.objectContainer);
        this.app.stage.addChild(this.robotContainer);
        
        // Initialize Grid Sprites & Overlay
        this.initGridSprites();
        this.drawGridOverlay();

        // Robot Container (for movement)
        this.robotContainer.zIndex = 10;

        // Robot Sprite (for internal animation)
        this.robotSprite = new PIXI.Sprite(AssetManager.textures.robot_basic);
        this.robotSprite.width = this.tileSize * 0.8; // Slightly smaller than tile
        this.robotSprite.height = this.tileSize * 0.8;
        this.robotSprite.anchor.set(0.5);
        this.robotContainer.addChild(this.robotSprite);
        
        // Add a glow filter or shadow to robot if possible, or just a sprite behind it
        const shadow = new PIXI.Graphics();
        shadow.fill({ color: 0x000000, alpha: 0.3 });
        shadow.ellipse(0, this.tileSize/3, this.tileSize/3, this.tileSize/6);
        shadow.fill();
        this.robotContainer.addChildAt(shadow, 0); // Add shadow behind robot
        
        // Idle Animation (Hover & Pulse)
        gsap.to(this.robotSprite, {
            y: '-=5',
            duration: 2,
            yoyo: true,
            repeat: -1,
            ease: "sine.inOut"
        });
        
        gsap.to(this.robotSprite.scale, {
            x: 1.05,
            y: 1.05,
            duration: 1.5,
            yoyo: true,
            repeat: -1,
            ease: "sine.inOut"
        });

        // Enable sorting for zIndex
        this.app.stage.sortableChildren = true;

        // Handle WebGL Context Loss
        this.app.canvas.addEventListener('webglcontextlost', (e) => {
            e.preventDefault();
            console.warn('WebGL Context Lost');
        });

        this.app.canvas.addEventListener('webglcontextrestored', () => {
            console.log('WebGL Context Restored');
            this.uploadTextures();
            this.initGridSprites();
            // Re-add robot
            if (this.robotSprite) {
                this.app.stage.addChild(this.robotSprite);
            }
        });
    }

    async uploadTextures() {
        // Create a dummy container with all textures to force upload
        const prepareContainer = new PIXI.Container();
        // Add a sprite for each texture to the container
        Object.values(AssetManager.textures).forEach(texture => {
            // Verify texture is valid (check for source in v8, baseTexture in v7)
            if (texture && (texture.source || texture.baseTexture)) {
                 const sprite = new PIXI.Sprite(texture);
                 prepareContainer.addChild(sprite);
            }
        });
        
        // Use prepare plugin if available (PixiJS v8) to upload to GPU
        if (this.app.renderer.prepare && typeof this.app.renderer.prepare.upload === 'function') {
            this.app.renderer.prepare.upload(prepareContainer);
        } else {
            // Fallback: Render once to force upload
            // This is the most reliable way to prevent "lazy initialization" warnings
            this.app.renderer.render(prepareContainer);
        }
        
        // Cleanup
        prepareContainer.destroy({ children: true });
    }

    setTheme(isDark) {
        if (!this.app) return;
        // Deep Space (Slate 950) vs Earthy Day (Slate 50)
        const color = isDark ? 0x020617 : 0xf8fafc; 
        this.app.renderer.background.color = color;
        
        // Update Grid Overlay opacity/color based on theme
        if (this.gridOverlay) {
            this.drawGridOverlay(isDark);
        }
    }

    drawGridOverlay(isDark = true) {
        if (!this.gridOverlay) return;
        this.gridOverlay.clear();
        
        const lineColor = isDark ? 0xffffff : 0x0f172a;
        const alpha = 0.05;

        this.gridOverlay.setStrokeStyle({ width: 1, color: lineColor, alpha: alpha });

        // Vertical lines
        for (let x = 0; x <= this.width; x++) {
            this.gridOverlay.moveTo(x * this.tileSize, 0);
            this.gridOverlay.lineTo(x * this.tileSize, this.height * this.tileSize);
        }
        this.gridOverlay.stroke();

        // Horizontal lines
        for (let y = 0; y <= this.height; y++) {
            this.gridOverlay.moveTo(0, y * this.tileSize);
            this.gridOverlay.lineTo(this.width * this.tileSize, y * this.tileSize);
        }
        this.gridOverlay.stroke();
    }

    setRobotType(type) {
        if (!this.robotSprite) return;
        const textureKey = `robot_${type}`;
        if (AssetManager.textures[textureKey]) {
            this.robotSprite.texture = AssetManager.textures[textureKey];
        } else {
            this.robotSprite.texture = AssetManager.textures.robot_basic;
        }
    }

    initGridSprites() {
        this.gridContainer.removeChildren();
        this.objectContainer.removeChildren();
        this.tileSprites = [];

        for (let y = 0; y < this.height; y++) {
            const row = [];
            for (let x = 0; x < this.width; x++) {
                // Base Soil
                const soil = new PIXI.Sprite(AssetManager.textures.soil);
                soil.width = this.tileSize;
                soil.height = this.tileSize;
                soil.x = x * this.tileSize;
                soil.y = y * this.tileSize;
                this.gridContainer.addChild(soil);

                // Object Layer (Plant, Rock, Water)
                const objectSprite = new PIXI.Sprite(PIXI.Texture.EMPTY);
                objectSprite.width = this.tileSize;
                objectSprite.height = this.tileSize;
                objectSprite.x = x * this.tileSize;
                objectSprite.y = y * this.tileSize;
                this.objectContainer.addChild(objectSprite);

                row.push({ base: soil, object: objectSprite });
            }
            this.tileSprites.push(row);
        }
    }

    updateGrid(grid) {
        if (!this.gridContainer || this.gridContainer.destroyed) return;
        
        // Use object pooling for better performance in incremental games
        for (let y = 0; y < grid.height; y++) {
            for (let x = 0; x < grid.width; x++) {
                const cellType = grid.getCell(x, y);
                const spritePair = this.tileSprites[y][x];
                
                // Update Object Layer
                if (cellType === TILE_TYPES.PLANT) {
                    spritePair.object.texture = AssetManager.textures.plant;
                } else if (cellType === TILE_TYPES.WATER) {
                    spritePair.object.texture = AssetManager.textures.water;
                } else if (cellType === TILE_TYPES.ROCK) {
                    spritePair.object.texture = AssetManager.textures.rock;
                } else {
                    spritePair.object.texture = PIXI.Texture.EMPTY;
                }
            }
        }
    }

    // New: Visual feedback for gains
    showFloatingText(text, x, y, color = 0x00ff00) {
        if (!this.app || !this.app.stage) return;

        const floatingText = new PIXI.Text({
            text: text,
            style: {
                fontFamily: 'Fira Code',
                fontSize: 20,
                fill: color,
                fontWeight: 'bold',
                stroke: { color: 0x000000, width: 3 }
            }
        });
        
        floatingText.x = x * this.tileSize + this.tileSize / 2;
        floatingText.y = y * this.tileSize;
        floatingText.anchor.set(0.5);
        
        this.app.stage.addChild(floatingText);

        gsap.to(floatingText, {
            y: floatingText.y - 60,
            alpha: 0,
            duration: 1.2,
            ease: "power2.out",
            onComplete: () => {
                if (floatingText.parent) {
                    floatingText.parent.removeChild(floatingText);
                }
                floatingText.destroy();
            }
        });
    }

    // Smoothly animate robot to new position
    moveRobot(x, y, duration = 0.3) {
        if (!this.robotContainer || this.robotContainer.destroyed) return;
        
        const targetX = x * this.tileSize + this.tileSize / 2;
        const targetY = y * this.tileSize + this.tileSize / 2;

        gsap.to(this.robotContainer, {
            x: targetX,
            y: targetY,
            duration: duration,
            ease: "power1.inOut"
        });
    }
    
    // Instant set for reset
    setRobotPos(x, y) {
        this.robotContainer.x = x * this.tileSize + this.tileSize / 2;
        this.robotContainer.y = y * this.tileSize + this.tileSize / 2;
    }

    destroy() {
        if (this.app) {
            // Destroy children, but keep textures and baseTextures to be reused by AssetManager
            this.app.destroy(true, { children: true, texture: false, baseTexture: false });
            this.app = null;
        }
    }
}
