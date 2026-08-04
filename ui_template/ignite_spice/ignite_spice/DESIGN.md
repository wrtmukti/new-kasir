---
name: Ignite & Spice
colors:
  surface: '#fef8f7'
  surface-dim: '#ded9d8'
  surface-bright: '#fef8f7'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f8f2f1'
  surface-container: '#f2edec'
  surface-container-high: '#ede7e6'
  surface-container-highest: '#e7e1e0'
  on-surface: '#1d1b1b'
  on-surface-variant: '#5b403d'
  inverse-surface: '#323030'
  inverse-on-surface: '#f5efee'
  outline: '#8f6f6c'
  outline-variant: '#e4beba'
  surface-tint: '#ba1a20'
  primary: '#af101a'
  on-primary: '#ffffff'
  primary-container: '#d32f2f'
  on-primary-container: '#fff2f0'
  inverse-primary: '#ffb3ac'
  secondary: '#9c4400'
  on-secondary: '#ffffff'
  secondary-container: '#fd7613'
  on-secondary-container: '#5b2500'
  tertiary: '#575757'
  on-tertiary: '#ffffff'
  tertiary-container: '#706f6f'
  on-tertiary-container: '#f6f3f3'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdad6'
  primary-fixed-dim: '#ffb3ac'
  on-primary-fixed: '#410003'
  on-primary-fixed-variant: '#930010'
  secondary-fixed: '#ffdbca'
  secondary-fixed-dim: '#ffb68f'
  on-secondary-fixed: '#331200'
  on-secondary-fixed-variant: '#773200'
  tertiary-fixed: '#e5e2e1'
  tertiary-fixed-dim: '#c8c6c5'
  on-tertiary-fixed: '#1b1c1c'
  on-tertiary-fixed-variant: '#474746'
  background: '#fef8f7'
  on-background: '#1d1b1b'
  surface-variant: '#e7e1e0'
typography:
  display-lg:
    fontFamily: Montserrat
    fontSize: 48px
    fontWeight: '800'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Montserrat
    fontSize: 32px
    fontWeight: '800'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Montserrat
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Montserrat
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  headline-md:
    fontFamily: Montserrat
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-bold:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1280px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 40px
---

## Brand & Style

The design system is built on a narrative of "Controlled Intensity." It balances a clean, minimalist structural foundation with high-energy, fiery visual accents to evoke a sense of passion, speed, and culinary precision. The target audience values bold experiences but requires professional-grade clarity and ease of use.

The aesthetic fuses **Minimalism** with **High-Contrast / Bold** elements. Large, confident typography and a stark, off-white canvas ensure readability, while vibrant red and orange "sparks" draw immediate attention to critical actions. The interface uses sharp lines and subtle, linear gradients to suggest movement and heat without cluttering the user's cognitive load.

## Colors

This design system utilizes a high-contrast palette designed to lead the eye through "heat maps" of importance. 

- **Primary (Chili Red):** Reserved for the most important calls to action and critical status indicators. It represents the "Ignite" aspect of the brand.
- **Secondary (Burnt Orange):** Used for supporting accents, hover states, and energetic highlights. 
- **Tertiary (Charcoal Smoke):** Provides the necessary weight and grounding for typography and structural borders.
- **Neutral (Bone White):** The #FFF9F8 base softens the traditional stark white to reduce eye strain while maintaining a clean, premium feel.

Gradients should be used sparingly, moving from Chili Red to Burnt Orange at a 135-degree angle to simulate a flickering flame effect on primary buttons or headers.

## Typography

Typography is a primary driver of the brand's energy. **Montserrat** is used for headlines to provide a bold, architectural presence. Its geometric nature allows for high-impact messaging. **Inter** is utilized for all functional text to ensure maximum legibility and a systematic, modern feel.

- **Headlines:** Use Bold (700) or ExtraBold (800) weights. Keep tracking tight on larger sizes to maintain a "dense" and powerful look.
- **Body:** Stick to Regular (400) for long-form text. Use Medium (500) for emphasis within paragraphs.
- **Labels:** Always use uppercase with slight letter spacing for technical metadata or small button text to ensure they don't get lost against the bold headlines.

## Layout & Spacing

The design system employs an **8px base grid** to ensure mathematical harmony across all components. The layout philosophy is a **Fixed-Fluid Hybrid**:
- **Desktop:** A 12-column grid with a maximum width of 1280px. Gutters are fixed at 24px to provide "breathing room" amidst the high-intensity colors.
- **Tablet:** An 8-column grid with 24px margins.
- **Mobile:** A 4-column fluid grid with 16px side margins.

Vertical rhythm should follow the 8px rule (8, 16, 24, 32, 48, 64). Use generous white space between major sections to prevent the bold Chili Red accents from becoming overwhelming.

## Elevation & Depth

To maintain a modern, minimalist aesthetic, this design system avoids heavy shadows. Depth is communicated through:

1.  **Tonal Stacking:** Surfaces use the Bone White (#FFF9F8) as the base layer. Raised cards or menus use pure white (#FFFFFF) to subtly lift them off the background.
2.  **Ghost Outlines:** Instead of shadows, use 1px solid borders in Charcoal Smoke at 10% opacity for container definition.
3.  **Active Elevation:** Only use shadows for "Active" states (e.g., a button being pressed or a card being hovered). These shadows should be tight and crisp: `0px 4px 12px rgba(33, 33, 33, 0.08)`.
4.  **Chromo-Depth:** High-saturation colors (Chili Red) naturally appear "closer" to the viewer, creating a functional hierarchy without physical metaphors.

## Shapes

The shape language is "Modern Geometric." By utilizing a **0.5rem (8px)** base corner radius, the UI feels approachable but retains its sharp, professional edge.

- **Standard Elements:** Buttons, Input fields, and Small cards use the 8px radius.
- **Large Containers:** Section containers or feature cards use 16px (rounded-lg) to create a softer frame for complex content.
- **Tags/Status:** Pills and small badges use the max "Pill" radius for distinct shape contrast against rectangular buttons.

## Components

### Buttons
- **Primary:** Gradient background (Chili Red to Burnt Orange), white Montserrat Bold text. No border.
- **Secondary:** Transparent background with a 2px solid Charcoal Smoke border.
- **Ghost:** Chili Red text, no background or border, used for low-priority actions.

### Input Fields
- **Default:** Bone White background, 1px Charcoal Smoke border (20% opacity).
- **Focus State:** Border changes to Chili Red (100% opacity) with a subtle 2px outer glow in Burnt Orange (10% opacity).

### Cards
- Pure White (#FFFFFF) background. 1px Charcoal Smoke border (10% opacity). Headers within cards should use Montserrat in Charcoal Smoke.

### Chips & Badges
- Used for categories or status. Small, all-caps Inter Medium. Use Burnt Orange for "Warm/In-Progress" and Chili Red for "Hot/Urgent."

### Interactive Elements
- **Checkboxes/Radios:** When selected, they should fill with the Chili Red primary color.
- **Lists:** Use 16px vertical padding between items with a light divider line (Charcoal Smoke at 5% opacity).