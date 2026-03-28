import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as mpatches
from matplotlib.patches import FancyBboxPatch, FancyArrowPatch
import numpy as np
import os

OUTPUT_DIR = os.path.dirname(os.path.abspath(__file__))

# ============================================================
# FIGURE 1: Incremental Methodology
# ============================================================
def draw_incremental_methodology():
    fig, ax = plt.subplots(figsize=(14, 8))
    ax.set_xlim(0, 14)
    ax.set_ylim(0, 8)
    ax.axis('off')
    ax.set_title('Figure 1: Incremental Methodology', fontsize=16, fontweight='bold', pad=20)

    phases = [
        ('Requirement\nAnalysis', '#3498db'),
        ('System\nDesign', '#2ecc71'),
        ('Implementation', '#e74c3c'),
        ('Testing', '#f39c12'),
        ('Deployment', '#9b59b6'),
    ]

    increments = [
        'Increment 1:\nUser Auth + Rooms',
        'Increment 2:\nReservations + Booking',
        'Increment 3:\nFood Orders + Menu',
        'Increment 4:\nPayments + Billing',
    ]

    for i, (phase, color) in enumerate(phases):
        x = 1.2 + i * 2.4
        box = FancyBboxPatch((x - 0.9, 5.8), 1.8, 1.2, boxstyle="round,pad=0.1",
                             facecolor=color, edgecolor='#2c3e50', linewidth=1.5, alpha=0.85)
        ax.add_patch(box)
        ax.text(x, 6.4, phase, ha='center', va='center', fontsize=9, fontweight='bold', color='white')
        if i < len(phases) - 1:
            ax.annotate('', xy=(x + 1.1, 6.4), xytext=(x + 0.9, 6.4),
                        arrowprops=dict(arrowstyle='->', color='#2c3e50', lw=2))

    for j, inc_text in enumerate(increments):
        y = 4.0 - j * 1.1
        box = FancyBboxPatch((1.5, y - 0.4), 11, 0.8, boxstyle="round,pad=0.1",
                             facecolor='#ecf0f1', edgecolor='#34495e', linewidth=1.2)
        ax.add_patch(box)
        ax.text(7, y, inc_text, ha='center', va='center', fontsize=9, fontweight='bold', color='#2c3e50')

        for i, (_, color) in enumerate(phases):
            cx = 2.5 + i * 2.4
            progress = min(1.0, max(0.0, (j * 1.3 - i * 0.3 + 0.5)))
            circle = plt.Circle((cx, y), 0.22, facecolor=color if progress > 0.3 else '#bdc3c7',
                                edgecolor='#2c3e50', linewidth=1, alpha=0.8)
            ax.add_patch(circle)
            if progress > 0.3:
                ax.text(cx, y, '✓', ha='center', va='center', fontsize=8, color='white', fontweight='bold')

    ax.text(7, 0.3, 'Each increment adds new features while previous features remain functional',
            ha='center', va='center', fontsize=10, style='italic', color='#7f8c8d')

    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, 'figure1_incremental_methodology.png'), dpi=200, bbox_inches='tight')
    plt.close()
    print("Figure 1: Incremental Methodology - DONE")


# ============================================================
# FIGURE 2: Use Case Diagram
# ============================================================
def draw_use_case():
    fig, ax = plt.subplots(figsize=(16, 12))
    ax.set_xlim(0, 16)
    ax.set_ylim(0, 12)
    ax.axis('off')
    ax.set_title('Figure 2: Use Case Diagram', fontsize=16, fontweight='bold', pad=20)

    # System boundary
    rect = FancyBboxPatch((4, 0.5), 8, 11, boxstyle="round,pad=0.2",
                          facecolor='#f8f9fa', edgecolor='#2c3e50', linewidth=2)
    ax.add_patch(rect)
    ax.text(8, 11.2, 'Hotel Management System', ha='center', va='center',
            fontsize=13, fontweight='bold', color='#2c3e50')

    def draw_actor(x, y, label):
        ax.plot(x, y + 0.5, 'o', markersize=12, color='#2c3e50')
        ax.plot([x, x], [y + 0.3, y - 0.1], color='#2c3e50', linewidth=2)
        ax.plot([x - 0.3, x + 0.3], [y + 0.15, y + 0.15], color='#2c3e50', linewidth=2)
        ax.plot([x - 0.2, x], [y - 0.5, y - 0.1], color='#2c3e50', linewidth=2)
        ax.plot([x + 0.2, x], [y - 0.5, y - 0.1], color='#2c3e50', linewidth=2)
        ax.text(x, y - 0.8, label, ha='center', va='center', fontsize=10, fontweight='bold')

    def draw_usecase(x, y, text, color='#dceefb'):
        ellipse = mpatches.Ellipse((x, y), 3.2, 0.7, facecolor=color,
                                    edgecolor='#2c3e50', linewidth=1.2)
        ax.add_patch(ellipse)
        ax.text(x, y, text, ha='center', va='center', fontsize=8.5, fontweight='bold')

    # Actors
    draw_actor(1.5, 9, 'Admin')
    draw_actor(1.5, 4, 'Guest')

    # Admin use cases
    admin_cases = [
        (7, 10.5, 'Manage Rooms (CRUD)'),
        (7, 9.7, 'Manage Reservations'),
        (7, 8.9, 'Check-In / Check-Out Guest'),
        (7, 8.1, 'Manage Food Menu'),
        (7, 7.3, 'Update Food Order Status'),
        (7, 6.5, 'Record Payment'),
        (7, 5.7, 'Create Guest Account'),
        (7, 4.9, 'View Dashboard & Reports'),
    ]

    guest_cases = [
        (7, 3.9, 'Register / Login'),
        (7, 3.1, 'Search & Filter Rooms'),
        (7, 2.3, 'Book a Room'),
        (7, 1.5, 'Order Food (During Check-In)'),
        (7, 0.7, 'View Billing Summary'),
    ]

    for x, y, text in admin_cases:
        draw_usecase(x, y, text, '#dceefb')
        ax.plot([2.0, x - 1.6], [9.0, y], color='#3498db', linewidth=0.8, alpha=0.6)

    for x, y, text in guest_cases:
        draw_usecase(x, y, text, '#d5f5e3')
        ax.plot([2.0, x - 1.6], [4.0, y], color='#27ae60', linewidth=0.8, alpha=0.6)

    # Shared use cases (admin also connects)
    for x, y, _ in guest_cases[:2]:
        ax.plot([2.0, x - 1.6], [9.0, y], color='#3498db', linewidth=0.8, linestyle='--', alpha=0.4)

    # Legend
    ax.text(13.5, 10.5, 'Legend:', fontsize=9, fontweight='bold')
    p1 = mpatches.Ellipse((13.5, 10.0), 1.0, 0.35, facecolor='#dceefb', edgecolor='#2c3e50', linewidth=0.8)
    ax.add_patch(p1)
    ax.text(14.2, 10.0, 'Admin', fontsize=8)
    p2 = mpatches.Ellipse((13.5, 9.5), 1.0, 0.35, facecolor='#d5f5e3', edgecolor='#2c3e50', linewidth=0.8)
    ax.add_patch(p2)
    ax.text(14.2, 9.5, 'Guest', fontsize=8)

    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, 'figure2_use_case_diagram.png'), dpi=200, bbox_inches='tight')
    plt.close()
    print("Figure 2: Use Case Diagram - DONE")


# ============================================================
# FIGURE 3: E-R Diagram
# ============================================================
def draw_er_diagram():
    fig, ax = plt.subplots(figsize=(18, 13))
    ax.set_xlim(0, 18)
    ax.set_ylim(0, 13)
    ax.axis('off')
    ax.set_title('Figure 3: Entity-Relationship (E-R) Diagram', fontsize=16, fontweight='bold', pad=20)

    def draw_entity(x, y, name, attrs, color='#3498db'):
        w, h_title = 3.2, 0.6
        h_attr = len(attrs) * 0.35 + 0.2
        total_h = h_title + h_attr

        title_box = FancyBboxPatch((x - w/2, y - h_title/2), w, h_title,
                                    boxstyle="round,pad=0.05", facecolor=color,
                                    edgecolor='#2c3e50', linewidth=1.5)
        ax.add_patch(title_box)
        ax.text(x, y, name, ha='center', va='center', fontsize=10, fontweight='bold', color='white')

        attr_box = FancyBboxPatch((x - w/2, y - h_title/2 - h_attr), w, h_attr,
                                   boxstyle="round,pad=0.05", facecolor='white',
                                   edgecolor='#2c3e50', linewidth=1)
        ax.add_patch(attr_box)

        for i, attr in enumerate(attrs):
            ay = y - h_title/2 - 0.25 - i * 0.35
            prefix = '[PK] ' if i == 0 else '     '
            ax.text(x - w/2 + 0.15, ay, prefix + attr, fontsize=7.5, va='center', fontfamily='monospace')

        return (x, y, w, h_title, h_attr, total_h)

    def draw_relation(e1_x, e1_y, e2_x, e2_y, label, card1, card2):
        ax.annotate('', xy=(e2_x, e2_y), xytext=(e1_x, e1_y),
                    arrowprops=dict(arrowstyle='->', color='#7f8c8d', lw=1.5))
        mid_x = (e1_x + e2_x) / 2
        mid_y = (e1_y + e2_y) / 2
        ax.text(mid_x, mid_y + 0.25, label, ha='center', va='center',
                fontsize=7.5, style='italic', color='#2c3e50',
                bbox=dict(boxstyle='round,pad=0.15', facecolor='#ffeaa7', edgecolor='none'))
        ax.text(e1_x + (e2_x - e1_x) * 0.15, e1_y + (e2_y - e1_y) * 0.15 + 0.2,
                card1, fontsize=7, fontweight='bold', color='#e74c3c')
        ax.text(e1_x + (e2_x - e1_x) * 0.85, e1_y + (e2_y - e1_y) * 0.85 + 0.2,
                card2, fontsize=7, fontweight='bold', color='#e74c3c')

    # Entities
    draw_entity(3, 11, 'USERS', [
        'id (PK)', 'name', 'email (UNIQUE)', 'password', 'phone', 'role (admin/guest)'
    ], '#2c3e50')

    draw_entity(9, 11, 'ROOMS', [
        'id (PK)', 'room_number (UNIQUE)', 'type', 'capacity',
        'price_per_night', 'status', 'floor'
    ], '#27ae60')

    draw_entity(6, 7, 'RESERVATIONS', [
        'id (PK)', 'user_id (FK)', 'room_id (FK)', 'check_in',
        'check_out', 'guests', 'status', 'total_amount'
    ], '#e74c3c')

    draw_entity(2, 3.5, 'FOODS', [
        'id (PK)', 'name', 'category', 'price', 'available'
    ], '#f39c12')

    draw_entity(7, 3.5, 'FOOD_ORDERS', [
        'id (PK)', 'reservation_id (FK)', 'food_id (FK)',
        'quantity', 'total_price', 'status'
    ], '#8e44ad')

    draw_entity(12, 3.5, 'PAYMENTS', [
        'id (PK)', 'reservation_id (FK)', 'amount',
        'method', 'status'
    ], '#2980b9')

    # Relationships
    draw_relation(3, 10.4, 6, 7.8, 'makes', '1', 'N')
    draw_relation(9, 10.2, 6, 7.8, 'assigned to', '1', 'N')
    draw_relation(6, 6.0, 7, 4.5, 'has', '1', 'N')
    draw_relation(2, 4.2, 7, 4.0, 'ordered as', '1', 'N')
    draw_relation(6, 6.0, 12, 4.5, 'paid via', '1', 'N')

    # Cardinality legend
    ax.text(15, 12, 'Cardinality:', fontsize=9, fontweight='bold')
    ax.text(15, 11.5, '1 = One', fontsize=8)
    ax.text(15, 11.1, 'N = Many', fontsize=8)
    ax.text(15, 10.5, 'PK = Primary Key', fontsize=8)
    ax.text(15, 10.1, 'FK = Foreign Key', fontsize=8)

    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, 'figure3_er_diagram.png'), dpi=200, bbox_inches='tight')
    plt.close()
    print("Figure 3: E-R Diagram - DONE")


# ============================================================
# FIGURE 4a: Data Flow Diagram Level 0 (Context)
# ============================================================
def draw_dfd_level0():
    fig, ax = plt.subplots(figsize=(14, 9))
    ax.set_xlim(0, 14)
    ax.set_ylim(0, 9)
    ax.axis('off')
    ax.set_title('Figure 4: Data Flow Diagram - Level 0 (Context Diagram)', fontsize=16, fontweight='bold', pad=20)

    # Central process
    circle = plt.Circle((7, 4.5), 2, facecolor='#3498db', edgecolor='#2c3e50', linewidth=2, alpha=0.85)
    ax.add_patch(circle)
    ax.text(7, 4.8, 'Hotel', ha='center', va='center', fontsize=14, fontweight='bold', color='white')
    ax.text(7, 4.2, 'Management', ha='center', va='center', fontsize=14, fontweight='bold', color='white')
    ax.text(7, 3.6, 'System', ha='center', va='center', fontsize=14, fontweight='bold', color='white')

    # External entities
    def draw_ext(x, y, label):
        box = FancyBboxPatch((x - 1.2, y - 0.4), 2.4, 0.8, boxstyle="round,pad=0.1",
                             facecolor='#2c3e50', edgecolor='#2c3e50', linewidth=1.5)
        ax.add_patch(box)
        ax.text(x, y, label, ha='center', va='center', fontsize=11, fontweight='bold', color='white')

    draw_ext(2, 7.5, 'Admin')
    draw_ext(12, 7.5, 'Guest')
    draw_ext(2, 1.5, 'Database')
    draw_ext(12, 1.5, 'Payment\nProcessor')

    # Data flows
    def draw_flow(x1, y1, x2, y2, label, offset=0.3):
        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle='->', color='#e74c3c', lw=1.8))
        mx, my = (x1 + x2) / 2, (y1 + y2) / 2
        ax.text(mx, my + offset, label, ha='center', va='center', fontsize=8,
                color='#2c3e50', fontweight='bold',
                bbox=dict(boxstyle='round,pad=0.15', facecolor='#ffeaa7', edgecolor='none'))

    # Admin flows
    draw_flow(2.8, 7.0, 5.2, 5.5, 'Room/Food/Guest\nManagement', 0.4)
    draw_flow(5.2, 5.0, 2.8, 6.8, 'Dashboard Data\nReports', -0.4)

    # Guest flows
    draw_flow(11.2, 7.0, 8.8, 5.5, 'Booking Request\nFood Order', 0.4)
    draw_flow(8.8, 5.0, 11.2, 6.8, 'Confirmation\nBilling Info', -0.4)

    # Database flows
    draw_flow(5.5, 3.0, 2.8, 1.8, 'Store Data', 0.3)
    draw_flow(2.8, 1.5, 5.5, 3.2, 'Retrieve Data', -0.3)

    # Payment flows
    draw_flow(8.5, 3.0, 11.2, 1.8, 'Payment Record', 0.3)
    draw_flow(11.2, 1.5, 8.5, 3.2, 'Payment Status', -0.3)

    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, 'figure4a_dfd_level0.png'), dpi=200, bbox_inches='tight')
    plt.close()
    print("Figure 4a: DFD Level 0 - DONE")


# ============================================================
# FIGURE 4b: Data Flow Diagram Level 1
# ============================================================
def draw_dfd_level1():
    fig, ax = plt.subplots(figsize=(16, 11))
    ax.set_xlim(0, 16)
    ax.set_ylim(0, 11)
    ax.axis('off')
    ax.set_title('Figure 4b: Data Flow Diagram - Level 1', fontsize=16, fontweight='bold', pad=20)

    def draw_process(x, y, num, label, color='#3498db'):
        circle = plt.Circle((x, y), 0.8, facecolor=color, edgecolor='#2c3e50', linewidth=1.5, alpha=0.85)
        ax.add_patch(circle)
        ax.text(x, y + 0.2, f'P{num}', ha='center', va='center', fontsize=8, fontweight='bold', color='white')
        ax.text(x, y - 0.2, label, ha='center', va='center', fontsize=7, fontweight='bold', color='white')

    def draw_store(x, y, label):
        box = FancyBboxPatch((x - 1.3, y - 0.25), 2.6, 0.5, boxstyle="round,pad=0.05",
                             facecolor='#ecf0f1', edgecolor='#2c3e50', linewidth=1.2)
        ax.add_patch(box)
        ax.plot([x - 1.3, x - 1.3], [y - 0.25, y + 0.25], color='#2c3e50', linewidth=2)
        ax.text(x, y, label, ha='center', va='center', fontsize=8, fontweight='bold')

    def draw_ext(x, y, label):
        box = FancyBboxPatch((x - 1, y - 0.3), 2, 0.6, boxstyle="round,pad=0.1",
                             facecolor='#2c3e50', edgecolor='#2c3e50', linewidth=1.5)
        ax.add_patch(box)
        ax.text(x, y, label, ha='center', va='center', fontsize=9, fontweight='bold', color='white')

    def draw_flow(x1, y1, x2, y2, label, offset=0.25):
        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle='->', color='#e74c3c', lw=1.2))
        mx, my = (x1 + x2) / 2, (y1 + y2) / 2
        ax.text(mx, my + offset, label, ha='center', va='center', fontsize=6.5,
                bbox=dict(boxstyle='round,pad=0.1', facecolor='#ffeaa7', edgecolor='none'))

    # External entities
    draw_ext(1.5, 10, 'Admin')
    draw_ext(14.5, 10, 'Guest')

    # Processes
    draw_process(4, 8.5, 1, 'Auth', '#2c3e50')
    draw_process(8, 8.5, 2, 'Room\nMgmt', '#27ae60')
    draw_process(12, 8.5, 3, 'Search\n& Filter', '#16a085')
    draw_process(4, 5.5, 4, 'Reservation\nMgmt', '#e74c3c')
    draw_process(8, 5.5, 5, 'Food\nOrder', '#f39c12')
    draw_process(12, 5.5, 6, 'Billing\nCalc', '#8e44ad')
    draw_process(8, 2.5, 7, 'Payment\nMgmt', '#2980b9')

    # Data stores
    draw_store(1.5, 5.5, 'D1: Users')
    draw_store(1.5, 3.5, 'D2: Rooms')
    draw_store(14.5, 5.5, 'D3: Reservations')
    draw_store(14.5, 3.5, 'D4: Foods')
    draw_store(4, 1.0, 'D5: Food Orders')
    draw_store(12, 1.0, 'D6: Payments')

    # Flows from Admin
    draw_flow(2.5, 10, 4, 9.3, 'Login')
    draw_flow(2.5, 9.7, 8, 9.2, 'Room CRUD')
    draw_flow(2.5, 9.5, 4, 6.3, 'Create Reservation')

    # Flows from Guest
    draw_flow(13.5, 10, 12, 9.3, 'Search Rooms')
    draw_flow(13.5, 9.7, 4, 9.0, 'Register/Login')
    draw_flow(13.8, 9.5, 12, 6.3, 'View Bill')

    # Process to store flows
    draw_flow(4, 7.7, 1.5, 5.8, 'User Data')
    draw_flow(8, 7.7, 1.5, 3.8, 'Room Data')
    draw_flow(4, 4.7, 14.5, 5.8, 'Reservation')
    draw_flow(8, 4.7, 4, 1.3, 'Order Data')
    draw_flow(8, 4.7, 14.5, 3.8, 'Food Data')
    draw_flow(8, 1.8, 12, 1.3, 'Payment Data')

    # Inter-process flows
    draw_flow(8.8, 5.5, 11.2, 5.5, 'Order Total')
    draw_flow(4.8, 5.5, 7.2, 5.5, 'Active Res.')
    draw_flow(8, 3.3, 8, 4.7, 'Paid Amount')
    draw_flow(12, 4.7, 8.8, 2.8, 'Balance Due')

    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, 'figure4b_dfd_level1.png'), dpi=200, bbox_inches='tight')
    plt.close()
    print("Figure 4b: DFD Level 1 - DONE")


# ============================================================
# FIGURE 5: System Architecture
# ============================================================
def draw_system_architecture():
    fig, ax = plt.subplots(figsize=(15, 10))
    ax.set_xlim(0, 15)
    ax.set_ylim(0, 10)
    ax.axis('off')
    ax.set_title('Figure 5: System Architecture (MVC Pattern)', fontsize=16, fontweight='bold', pad=20)

    def draw_box(x, y, w, h, label, sublabel, color, fontsize=11):
        box = FancyBboxPatch((x, y), w, h, boxstyle="round,pad=0.15",
                             facecolor=color, edgecolor='#2c3e50', linewidth=1.5, alpha=0.9)
        ax.add_patch(box)
        ax.text(x + w/2, y + h/2 + 0.15, label, ha='center', va='center',
                fontsize=fontsize, fontweight='bold', color='white')
        if sublabel:
            ax.text(x + w/2, y + h/2 - 0.25, sublabel, ha='center', va='center',
                    fontsize=7.5, color='white', alpha=0.9)

    # Client Layer
    draw_box(0.5, 8.5, 14, 1.2, 'CLIENT (Browser)', 'HTML / CSS / Bootstrap 5 / JavaScript', '#34495e')

    # Arrow
    ax.annotate('', xy=(7.5, 8.3), xytext=(7.5, 8.5), arrowprops=dict(arrowstyle='<->', color='#e74c3c', lw=2))
    ax.text(9, 8.0, 'HTTP Request / Response', fontsize=8, color='#e74c3c', fontweight='bold')

    # Web Server
    draw_box(0.5, 6.8, 14, 1.0, 'Laravel Framework (PHP)', 'Routes → Middleware → Controller → Response', '#2c3e50')

    # MVC Layer
    draw_box(0.5, 4.5, 4, 2.0, 'VIEWS', 'Blade Templates\n(20 .blade.php files)', '#3498db')
    draw_box(5.5, 4.5, 4, 2.0, 'CONTROLLERS', '9 Controllers\n(Auth, Room, Reservation\nFood, Payment, etc.)', '#e74c3c')
    draw_box(10.5, 4.5, 4, 2.0, 'MODELS', '6 Eloquent Models\n(User, Room, Reservation\nFood, FoodOrder, Payment)', '#27ae60')

    # Arrows between MVC
    ax.annotate('', xy=(5.3, 5.5), xytext=(4.7, 5.5), arrowprops=dict(arrowstyle='<->', color='#7f8c8d', lw=1.5))
    ax.annotate('', xy=(10.3, 5.5), xytext=(9.7, 5.5), arrowprops=dict(arrowstyle='<->', color='#7f8c8d', lw=1.5))

    # Middleware + Algorithms layer
    draw_box(0.5, 2.8, 6.5, 1.3, 'MIDDLEWARE & AUTH', 'AdminMiddleware (RBAC)\nSession-based Authentication', '#8e44ad')
    draw_box(8, 2.8, 6.5, 1.3, 'ALGORITHMS', 'Interval Overlap Detection\nSearch, Filter & Sort\nBilling Aggregation', '#f39c12')

    # Database layer
    draw_box(0.5, 0.8, 14, 1.5, 'DATABASE (MySQL)', 'Tables: users, rooms, reservations, foods, food_orders, payments, sessions', '#c0392b')

    # Arrows to database
    ax.annotate('', xy=(7.5, 2.6), xytext=(7.5, 2.3), arrowprops=dict(arrowstyle='<->', color='#e74c3c', lw=2))
    ax.text(9, 2.35, 'Eloquent ORM Queries', fontsize=8, color='#e74c3c', fontweight='bold')

    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, 'figure5_system_architecture.png'), dpi=200, bbox_inches='tight')
    plt.close()
    print("Figure 5: System Architecture - DONE")


# ============================================================
# FIGURE 6: Database Schema Design
# ============================================================
def draw_database_schema():
    fig, ax = plt.subplots(figsize=(20, 14))
    ax.set_xlim(0, 20)
    ax.set_ylim(0, 14)
    ax.axis('off')
    ax.set_title('Figure 6: Database Schema Design', fontsize=16, fontweight='bold', pad=20)

    def draw_table(x, y, name, columns, color='#3498db'):
        w = 3.8
        h_title = 0.5
        row_h = 0.32
        h_body = len(columns) * row_h + 0.15
        total_h = h_title + h_body

        # Title
        title_box = FancyBboxPatch((x, y), w, h_title, boxstyle="round,pad=0.03",
                                    facecolor=color, edgecolor='#2c3e50', linewidth=1.5)
        ax.add_patch(title_box)
        ax.text(x + w/2, y + h_title/2, name, ha='center', va='center',
                fontsize=10, fontweight='bold', color='white')

        # Body
        body_box = FancyBboxPatch((x, y - h_body), w, h_body, boxstyle="round,pad=0.03",
                                   facecolor='white', edgecolor='#2c3e50', linewidth=1)
        ax.add_patch(body_box)

        for i, (col_name, col_type, key) in enumerate(columns):
            cy = y - 0.12 - i * row_h
            key_str = ''
            key_color = '#2c3e50'
            if key == 'PK':
                key_str = 'PK '
                key_color = '#e74c3c'
            elif key == 'FK':
                key_str = 'FK '
                key_color = '#3498db'

            ax.text(x + 0.15, cy, key_str, fontsize=7, va='center', fontweight='bold',
                    color=key_color, fontfamily='monospace')
            ax.text(x + 0.55, cy, col_name, fontsize=7.5, va='center', fontfamily='monospace')
            ax.text(x + w - 0.15, cy, col_type, fontsize=6.5, va='center', ha='right',
                    color='#7f8c8d', fontfamily='monospace')

        return (x, y, w, total_h, h_body)

    # Draw tables
    users = draw_table(0.5, 12.5, 'users', [
        ('id', 'BIGINT', 'PK'),
        ('name', 'VARCHAR(255)', ''),
        ('email', 'VARCHAR(255) UNIQUE', ''),
        ('password', 'VARCHAR(255)', ''),
        ('phone', 'VARCHAR(255) NULL', ''),
        ('role', "ENUM('admin','guest')", ''),
        ('remember_token', 'VARCHAR(100) NULL', ''),
        ('created_at', 'TIMESTAMP', ''),
        ('updated_at', 'TIMESTAMP', ''),
    ])

    rooms = draw_table(5.5, 12.5, 'rooms', [
        ('id', 'BIGINT', 'PK'),
        ('room_number', 'VARCHAR UNIQUE', ''),
        ('type', "ENUM('single',...)", ''),
        ('capacity', 'INT', ''),
        ('price_per_night', 'DECIMAL(10,2)', ''),
        ('status', "ENUM('available',...)", ''),
        ('floor', 'INT', ''),
        ('created_at', 'TIMESTAMP', ''),
        ('updated_at', 'TIMESTAMP', ''),
    ])

    reservations = draw_table(10.5, 12.5, 'reservations', [
        ('id', 'BIGINT', 'PK'),
        ('user_id', 'BIGINT', 'FK'),
        ('room_id', 'BIGINT', 'FK'),
        ('check_in', 'DATE', ''),
        ('check_out', 'DATE', ''),
        ('guests', 'INT', ''),
        ('status', "ENUM('pending',...)", ''),
        ('total_amount', 'DECIMAL(10,2)', ''),
        ('created_at', 'TIMESTAMP', ''),
        ('updated_at', 'TIMESTAMP', ''),
    ])

    foods = draw_table(0.5, 6.5, 'foods', [
        ('id', 'BIGINT', 'PK'),
        ('name', 'VARCHAR(255)', ''),
        ('category', "ENUM('breakfast',...)", ''),
        ('price', 'DECIMAL(8,2)', ''),
        ('available', 'BOOLEAN', ''),
        ('created_at', 'TIMESTAMP', ''),
        ('updated_at', 'TIMESTAMP', ''),
    ])

    food_orders = draw_table(5.5, 6.5, 'food_orders', [
        ('id', 'BIGINT', 'PK'),
        ('reservation_id', 'BIGINT', 'FK'),
        ('food_id', 'BIGINT', 'FK'),
        ('quantity', 'INT', ''),
        ('total_price', 'DECIMAL(8,2)', ''),
        ('status', "ENUM('pending',...)", ''),
        ('created_at', 'TIMESTAMP', ''),
        ('updated_at', 'TIMESTAMP', ''),
    ])

    payments = draw_table(10.5, 6.5, 'payments', [
        ('id', 'BIGINT', 'PK'),
        ('reservation_id', 'BIGINT', 'FK'),
        ('amount', 'DECIMAL(10,2)', ''),
        ('method', "ENUM('cash',...)", ''),
        ('status', "ENUM('pending',...)", ''),
        ('created_at', 'TIMESTAMP', ''),
        ('updated_at', 'TIMESTAMP', ''),
    ])

    sessions = draw_table(15.5, 12.5, 'sessions', [
        ('id', 'VARCHAR(255)', 'PK'),
        ('user_id', 'BIGINT NULL', 'FK'),
        ('ip_address', 'VARCHAR(45)', ''),
        ('user_agent', 'TEXT', ''),
        ('payload', 'LONGTEXT', ''),
        ('last_activity', 'INT', ''),
    ])

    # Draw FK relationships
    def draw_fk(x1, y1, x2, y2, color='#e74c3c'):
        ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                    arrowprops=dict(arrowstyle='->', color=color, lw=1.5,
                                    connectionstyle='arc3,rad=0.1'))

    # users -> reservations (user_id)
    draw_fk(4.3, 11.5, 10.5, 11.8)
    # rooms -> reservations (room_id)
    draw_fk(9.3, 11.5, 10.5, 11.5)
    # reservations -> food_orders (reservation_id)
    draw_fk(11.5, 8.8, 7.8, 6.5)
    # foods -> food_orders (food_id)
    draw_fk(4.3, 5.8, 5.5, 5.8)
    # reservations -> payments (reservation_id)
    draw_fk(12, 8.8, 12, 6.5)
    # users -> sessions (user_id)
    draw_fk(4.3, 12.2, 15.5, 12.2)

    # Legend
    ax.text(15.5, 5.0, 'Legend:', fontsize=10, fontweight='bold')
    ax.text(15.5, 4.5, 'PK = Primary Key', fontsize=9, color='#e74c3c', fontweight='bold')
    ax.text(15.5, 4.1, 'FK = Foreign Key', fontsize=9, color='#3498db', fontweight='bold')
    ax.text(15.5, 3.7, '→  = FK Relationship', fontsize=9, color='#e74c3c')

    # Relationship labels
    ax.text(15.5, 3.0, 'Relationships:', fontsize=10, fontweight='bold')
    ax.text(15.5, 2.5, 'users 1 ─── N reservations', fontsize=8, fontfamily='monospace')
    ax.text(15.5, 2.1, 'rooms 1 ─── N reservations', fontsize=8, fontfamily='monospace')
    ax.text(15.5, 1.7, 'reservations 1 ── N food_orders', fontsize=8, fontfamily='monospace')
    ax.text(15.5, 1.3, 'foods 1 ─── N food_orders', fontsize=8, fontfamily='monospace')
    ax.text(15.5, 0.9, 'reservations 1 ── N payments', fontsize=8, fontfamily='monospace')

    plt.tight_layout()
    plt.savefig(os.path.join(OUTPUT_DIR, 'figure6_database_schema.png'), dpi=200, bbox_inches='tight')
    plt.close()
    print("Figure 6: Database Schema Design - DONE")


# ============================================================
# RUN ALL
# ============================================================
if __name__ == '__main__':
    print("Generating diagrams...\n")
    draw_incremental_methodology()
    draw_use_case()
    draw_er_diagram()
    draw_dfd_level0()
    draw_dfd_level1()
    draw_system_architecture()
    draw_database_schema()
    print(f"\nAll diagrams saved to: {OUTPUT_DIR}")
