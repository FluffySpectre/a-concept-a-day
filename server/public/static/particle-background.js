class Particle {
    constructor(x, y, vel_x, vel_y, diameter, speed) {
        this.position = createVector(x, y);
        this.velocity = createVector(vel_x, vel_y).mult(speed);
        this.diameter = diameter;
    }

    update() {
        this.position.add(this.velocity);
        this.wrapAround();
    }

    show() {
        noStroke();
        fill(100, 75);
        ellipse(this.position.x, this.position.y, this.diameter, this.diameter);
    }

    wrapAround() {
        if (this.position.x > windowWidth) {
            this.position.x = 0;
        } else if (this.position.x < 0) {
            this.position.x = windowWidth;
        }

        if (this.position.y > windowHeight) {
            this.position.y = 0;
        } else if (this.position.y < 0) {
            this.position.y = windowHeight;
        }
    }
}

function calculateParticleAmount(density) {
    const width = windowWidth;
    const height = windowHeight;
    const area = width * height;
    return area * density;
}

let bodyBackground = 240;
let numParticles = 0;
const particles = [];
const particleDensity = 0.0002;

function setup() {
    createCanvas(windowWidth, windowHeight);

    numParticles = calculateParticleAmount(particleDensity);
    for (let i = 0; i < numParticles; i++) {
        particles.push(new Particle(random(0, windowWidth), random(0, windowHeight), random(-1, 1), random(-1, 1), random(3, 6), 0.3));
    }

    // Detect dark mode and set the background color accordingly
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        bodyBackground = 10;
    }

    frameRate(30);
}

function draw() {
    // background(240); // light
    background(bodyBackground); // dark
    
    for (let i = 0; i < numParticles; i++) {
        particles[i].update();
        particles[i].show();
    }

    // draw lines between particles, which are close to each other
    for (let i = 0; i < numParticles; i++) {
        for (let j = i + 1; j < numParticles; j++) {
            const distance = particles[i].position.dist(particles[j].position);

            if (distance < 100) {
                // Calculate the width of the line based on the distance between the particles
                const lineWidth = map(distance, 0, 100, 2, 0.1);

                // Calculate the alpha value based on the distance between the particles
                const alpha = map(distance, 0, 100, 200, 0);

                stroke(120, alpha);
                strokeWeight(lineWidth);
                line(particles[i].position.x, particles[i].position.y, particles[j].position.x, particles[j].position.y);
            }
        }
    }
}

function windowResized() {
    resizeCanvas(windowWidth, windowHeight);
}
