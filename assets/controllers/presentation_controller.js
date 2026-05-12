import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["section", "dot", "rolePreview", "roleBtn"];
    static values = {
        currentIndex: Number,
    };

    connect() {
        this.currentIndexValue = 0;
        this.updateNavigation();
        
        window.addEventListener("keydown", this.handleKeydown.bind(this));
        
        // Listen for scroll to update dots
        this.element.addEventListener("scroll", this.handleScroll.bind(this));
        
        console.log("Presentation Controller Connected");
    }

    handleKeydown(event) {
        if (event.key === "ArrowDown" || event.key === "ArrowRight" || event.key === "PageDown" || event.key === " ") {
            this.next();
        } else if (event.key === "ArrowUp" || event.key === "ArrowLeft" || event.key === "PageUp") {
            this.previous();
        }
    }

    next() {
        if (this.currentIndexValue < this.sectionTargets.length - 1) {
            this.currentIndexValue++;
            this.scrollToCurrent();
        }
    }

    previous() {
        if (this.currentIndexValue > 0) {
            this.currentIndexValue--;
            this.scrollToCurrent();
        }
    }

    goTo(event) {
        const index = parseInt(event.currentTarget.dataset.index);
        this.currentIndexValue = index;
        this.scrollToCurrent();
    }

    scrollToCurrent() {
        this.sectionTargets[this.currentIndexValue].scrollIntoView({ behavior: "smooth" });
        this.updateNavigation();
    }

    handleScroll() {
        const scrollPosition = this.element.scrollTop;
        const sectionHeight = window.innerHeight;
        const newIndex = Math.round(scrollPosition / sectionHeight);
        
        if (newIndex !== this.currentIndexValue) {
            this.currentIndexValue = newIndex;
            this.updateNavigation();
        }
    }

    updateNavigation() {
        this.dotTargets.forEach((dot, index) => {
            dot.classList.toggle("active", index === this.currentIndexValue);
        });
    }

    switchRole(event) {
        const role = event.currentTarget.dataset.role;
        
        // Update buttons
        this.roleBtnTargets.forEach(btn => {
            btn.classList.toggle("active", btn.dataset.role === role);
        });

        // Update preview content (placeholder logic for now)
        const preview = this.rolePreviewTarget;
        preview.style.opacity = "0";
        
        setTimeout(() => {
            preview.innerHTML = this.getRoleContent(role);
            preview.style.opacity = "1";
        }, 300);
    }

    getRoleContent(role) {
        const icons   = { student: 'person', teacher: 'school', admin: 'admin_panel_settings' };
        const colors  = { student: '#3b82f6', teacher: '#10b981', admin: '#f59e0b' };
        const labels  = { student: 'STUDENT PORTAL', teacher: 'TEACHER DASHBOARD', admin: 'ADMIN CONTROL CENTER' };
        const images  = {
            student: '/images/student view.png',
            teacher: '/images/teacher view.png',
            admin:   '/images/admin view.png',
        };

        const color = colors[role], icon = icons[role], label = labels[role], img = images[role];

        return `
            <div class="glass-card" style="padding:0; overflow:hidden; border-color:${color}55; height:100%; display:flex; flex-direction:column;">
                <div style="padding:12px 20px; background:${color}18; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:10px; flex-shrink:0;">
                    <span class="material-symbols-rounded" style="color:${color}; font-size:16px;">${icon}</span>
                    <span style="font-weight:800; letter-spacing:1px; font-size:0.8rem;">${label}</span>
                </div>
                <div style="padding:16px; flex:1; display:flex; align-items:center; justify-content:center; min-height:0;">
                    <div style="background:#0f172a; border-radius:10px; border:1px solid rgba(255,255,255,0.1); overflow:hidden; display:flex; flex-direction:column; height:100%;">
                        <div style="height:22px; background:#1e293b; display:flex; align-items:center; gap:5px; padding:0 10px; flex-shrink:0;">
                            <div style="width:7px; height:7px; border-radius:50%; background:#ef4444;"></div>
                            <div style="width:7px; height:7px; border-radius:50%; background:#fbbf24;"></div>
                            <div style="width:7px; height:7px; border-radius:50%; background:#10b981;"></div>
                        </div>
                        <img src="${img}" style="flex:1; min-height:0; width:auto; object-fit:contain; display:block;">
                    </div>
                </div>
            </div>
        `;
    }
}
