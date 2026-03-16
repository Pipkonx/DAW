import { TILE_TYPES } from './Constants';

export class Grid {
    constructor(width, height) {
        this.width = width;
        this.height = height;
        this.cells = Array(height).fill(0).map(() => Array(width).fill(TILE_TYPES.SOIL));
    }

    getCell(x, y) {
        if (this.isValid(x, y)) return this.cells[y][x];
        return null;
    }

    setCell(x, y, type) {
        if (this.isValid(x, y)) {
            this.cells[y][x] = type;
            return true;
        }
        return false;
    }

    isValid(x, y) {
        return x >= 0 && x < this.width && y >= 0 && y < this.height;
    }
}
