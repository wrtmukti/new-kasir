---
name: Nusantara Hearth
colors:
  surface: '#fcf9f2'
  surface-dim: '#dcdad3'
  surface-bright: '#fcf9f2'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3ec'
  surface-container: '#f0eee7'
  surface-container-high: '#ebe8e1'
  surface-container-highest: '#e5e2db'
  on-surface: '#1c1c18'
  on-surface-variant: '#56423e'
  inverse-surface: '#31312c'
  inverse-on-surface: '#f3f0ea'
  outline: '#89726d'
  outline-variant: '#dcc1ba'
  surface-tint: '#9c432d'
  primary: '#943d28'
  on-primary: '#ffffff'
  primary-container: '#b3543d'
  on-primary-container: '#fff3f0'
  inverse-primary: '#ffb4a3'
  secondary: '#725a42'
  on-secondary: '#ffffff'
  secondary-container: '#fedcbe'
  on-secondary-container: '#796048'
  tertiary: '#435f40'
  on-tertiary: '#ffffff'
  tertiary-container: '#5b7857'
  on-tertiary-container: '#dfffd7'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdad2'
  primary-fixed-dim: '#ffb4a3'
  on-primary-fixed: '#3d0700'
  on-primary-fixed-variant: '#7d2c18'
  secondary-fixed: '#fedcbe'
  secondary-fixed-dim: '#e1c1a4'
  on-secondary-fixed: '#291806'
  on-secondary-fixed-variant: '#59422c'
  tertiary-fixed: '#cbebc3'
  tertiary-fixed-dim: '#afcfa9'
  on-tertiary-fixed: '#062108'
  on-tertiary-fixed-variant: '#324d30'
  background: '#fcf9f2'
  on-background: '#1c1c18'
  surface-variant: '#e5e2db'
typography:
  headline-xl:
    fontFamily: Playfair Display
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-xl-mobile:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: Manrope
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Manrope
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 40px
  gutter: 16px
  margin-mobile: 20px
  margin-desktop: 64px
---

## Brand & Style

The design system is rooted in the warmth of Indonesian village life—specifically the "Kampung" aesthetic. It evokes a sense of slow living, community (Gotong Royong), and craftsmanship. The interface should feel like stepping into a traditional wooden pavilion (Joglo): breezy, authentic, and grounded in nature.

The style is **Tactile & Organic**, blending traditional textures with a modern functional layout. We avoid clinical perfection in favor of "wabi-sabi" inspired warmth. Surfaces should feel like they are made of clay, wood, or woven fiber rather than plastic or glass. The emotional response is one of nostalgia, comfort, and reliability.

## Colors

The palette is derived from natural building materials found in a traditional village setting.

*   **Primary (Terracotta):** Used for primary actions, highlight states, and key brand elements. It represents sun-baked clay tiles.
*   **Secondary (Teak Wood):** Used for structural elements, headers, and navigation to provide a sense of stability and permanence.
*   **Tertiary (Sage Green):** Used for success states, dietary tags (vegan/halal), and environmental accents. It represents the lush flora surrounding the village.
*   **Background (Warm Cream):** A non-white base that reduces eye strain and mimics the texture of handmade paper or lime-washed walls.
*   **Text (Charcoal):** A softened black that maintains high legibility while feeling less harsh than pure hex #000000.

## Typography

This design system utilizes a high-contrast typographic pairing to balance tradition with modern utility.

*   **Headlines (Playfair Display):** These should be treated with editorial care. Use these for product names, section titles, and marketing messages. The slight rustic elegance of the serifs provides the "traditional" anchor for the brand.
*   **Body & Labels (Manrope):** A clean, geometric sans-serif that ensures high legibility for menus, prices, and descriptions. Manrope’s modern proportions keep the app from feeling "dated" while maintaining a friendly, open character.

## Layout & Spacing

The layout follows a **Fluid Grid** model with generous white space to mimic the airy nature of a village square. 

*   **Grid:** A 12-column grid for desktop and a 4-column grid for mobile. 
*   **Rhythm:** We use an 8px base unit. Vertical spacing between sections should be ample (`xl` or 40px) to allow the "paper" background to breathe.
*   **Safe Areas:** Mobile screens utilize a 20px side margin to ensure content doesn't feel cramped against the edge of the device.

## Elevation & Depth

To maintain the rustic aesthetic, we avoid heavy, synthetic shadows. Depth is communicated through **Tonal Layering** and **Subtle Materiality**.

1.  **Level 0 (Base):** The Cream background (`#fcf9f2`). Use a very subtle, low-opacity tileable pattern of *Gedhek* (woven bamboo) as a background overlay (opacity 3-5%).
2.  **Level 1 (Cards):** Use a slightly lighter cream or pure white with a 1px solid border in a faint Teak color (`#4b3621` at 10% opacity). 
3.  **Level 2 (Interactive):** When an element is lifted (like a card being hovered), use a soft, large-radius shadow tinted with the Teak brown rather than grey.
4.  **Floating Elements:** Buttons and floating action buttons (FABs) use the Terracotta color to pop against the neutral base, relying on color contrast rather than extreme shadow depth.

## Shapes

The shape language is **Organic and Soft**. We avoid sharp 90-degree corners to reflect the handmade nature of village architecture.

*   **Standard Elements (Buttons, Inputs):** 0.5rem (8px) corner radius.
*   **Large Containers (Cards, Modals):** 1rem (16px) corner radius.
*   **Media/Images:** Use the `rounded-lg` (1rem) setting. Food photography should always have softened corners to feel more inviting.

## Components

### Buttons
*   **Primary:** Solid Terracotta background with White text. Heavy enough to stand out but with the standard 8px rounding.
*   **Secondary:** Teak Wood outline (1.5px) with Teak text.
*   **Tertiary/Text:** Sage Green text for "soft" actions like 'Add Note' or 'View More'.

### Cards
*   Cards represent "plates" or "trays." They should have a subtle 1px border.
*   Incorporate a small Batik-inspired motif in the corner of category cards to distinguish between "Coffee," "Meals," and "Sweets."

### Input Fields
*   Use a "Field" style: A light-colored fill (5% Teak) with a bottom-border only, or a fully enclosed box with a very thin border. 
*   Placeholders should be in a muted Charcoal.

### Chips & Tags
*   Use for dietary requirements or stock status. 
*   Shapes should be fully rounded (pill-shaped) to contrast against the more structured cards. 
*   Backgrounds should be Sage Green for "Available/Fresh" and Teak for "Sold Out."

### Icons
*   Icons should be monoline but with slightly "imperfect" strokes to mimic hand-drawing.
*   Avoid sharp, technical icons; prefer icons with rounded ends and organic curves.