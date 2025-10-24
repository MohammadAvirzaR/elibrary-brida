import tailwindcssAnimate from 'tailwindcss-animate'

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ['class'],
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      /* ===== 🎨 COLOR SYSTEM - Design System BRIDA ===== */
      colors: {
        // Primary Colors (Blue Theme - Sidebar & Main Actions)
        primary: {
          DEFAULT: '#084CE5', // Submit, Add, Update, Lanjut buttons
          dark: '#0D47A1', // Sidebar background, branding
          hover: '#084CE580', // 50% opacity hover
          light: '#8CAFFF', // Login button hover
        },

        // Secondary Colors (Success/Approve Actions)
        secondary: {
          DEFAULT: '#4CAF50', // SSO button, success states
          dark: '#1F8C4A', // Continue button, approve
          hover: '#1F8C4A80', // 50% opacity hover
          light: '#48B16E', // Success icon (toast notification)
        },

        // Accent Colors (Neutral Actions & Highlights)
        accent: {
          DEFAULT: '#FF9800', // Summary cards, edit icons, tags
          hover: '#FF980080', // 50% opacity hover
          light: '#FFB84D',
        },

        // Danger/Error Colors (Delete, Reject, Cancel)
        danger: {
          DEFAULT: '#CD1919', // Cancel, delete, reject buttons
          hover: '#CD191980', // 50% opacity hover
          dark: '#B01515',
          light: '#F44336', // Delete icon in tables
        },

        // Info/Interactive Colors
        info: {
          DEFAULT: '#00A9FF', // Link text, table titles, progress
          light: '#007BFF', // Table header background (5% opacity)
          dark: '#0088CC',
        },

        // Neutral/Gray Scale (Optimized for Accessibility)
        neutral: {
          50: '#FAFAFA', // Lightest background
          100: '#F0F1FA', // Bulk actions background, type tags
          200: '#ECE6F0', // Search bar background
          300: '#D9D9D9', // Border, stroke, disabled states
          400: '#B3B3B3', // Input placeholder text
          500: '#8F97A3', // Table text, secondary info
          600: '#5F6368', // Improved contrast for body text
          700: '#49454F', // Search bar text/icon
          800: '#333333', // Secondary text (paragraphs, descriptions)
          900: '#1E1E1E', // Primary text (labels, titles)
          950: '#000000', // Heading text, icons
        },

        // Semantic Background Colors
        background: {
          DEFAULT: '#FFFFFF', // Card, form, input backgrounds
          page: '#FAF9F9', // Page/frame background
          header: '#89CFF3', // Dashboard header
          sidebar: '#0D47A1', // Admin sidebar
        },

        // UI State Colors
        success: '#48B16E', // Toast success icon
        warning: '#FF9800', // Same as accent
        error: '#CD1919', // Same as danger

        // Specialized Colors
        border: {
          DEFAULT: '#D9D9D9', // Input, form borders
          dark: '#000000', // Emphasized borders
          light: '#ECE6F0', // Subtle dividers
        },

        // PDF Icon Color
        pdf: '#EF5350',
      },

      /* ===== 🖋️ TYPOGRAPHY SYSTEM ===== */
      fontFamily: {
        // Simplified to 3 main fonts (reduced from 7)
        heading: ['Inter', 'Poppins', 'Montserrat', 'sans-serif'], // Inter as primary
        body: ['Inter', 'Roboto', 'sans-serif'], // Inter + Roboto fallback
        mono: ['Monaco', 'Courier New', 'monospace'], // For code snippets
      },

      fontSize: {
        // Heading Sizes (from PDF specs)
        'heading-xl': ['32px', { lineHeight: '1.3', fontWeight: '700' }], // Page titles
        'heading-lg': ['24px', { lineHeight: '1.4', fontWeight: '700' }], // Section headers
        'heading-md': ['20px', { lineHeight: '1.5', fontWeight: '700' }], // Sub-sections
        'heading-sm': ['16px', { lineHeight: '1.5', fontWeight: '700' }], // Card titles

        // Body Text Sizes
        'body-lg': ['20px', { lineHeight: '1.6', fontWeight: '400' }], // Large body
        'body-md': ['16px', { lineHeight: '1.6', fontWeight: '400' }], // Default body
        'body-sm': ['14px', { lineHeight: '1.5', fontWeight: '400' }], // Small text
        'body-xs': ['12px', { lineHeight: '1.4', fontWeight: '400' }], // Captions
        'body-xxs': ['11px', { lineHeight: '1.3', fontWeight: '400' }], // Micro text

        // Button Text Sizes
        'btn-lg': ['24px', { lineHeight: '1', fontWeight: '700' }], // Large CTAs
        'btn-md': ['20px', { lineHeight: '1', fontWeight: '700' }], // Primary buttons
        'btn-sm': ['16px', { lineHeight: '1', fontWeight: '700' }], // Secondary buttons
        'btn-xs': ['15px', { lineHeight: '1', fontWeight: '700' }], // Small actions
      },

      fontWeight: {
        light: '300',
        regular: '400',
        medium: '500',
        semibold: '600',
        bold: '700',
        extrabold: '800',
      },

      /* ===== 🔲 BORDER RADIUS SYSTEM ===== */
      borderRadius: {
        none: '0',
        sm: '4px',
        DEFAULT: '10px', // Buttons, small components
        md: '10px', // Alias for default
        lg: '20px', // Cards, forms, containers
        xl: '24px', // Toast notifications
        '2xl': '50px', // Circular buttons/avatars
        full: '9999px', // Fully circular
      },

      /* ===== 📦 BOX SHADOW SYSTEM ===== */
      boxShadow: {
        none: 'none',
        sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        DEFAULT: '0 2px 6px rgba(0, 0, 0, 0.1)', // Soft shadow
        md: '0 4px 10px rgba(0, 0, 0, 0.05)', // Card shadow
        lg: '0 8px 16px rgba(0, 0, 0, 0.08)', // Elevated components
        xl: '0 12px 24px rgba(0, 0, 0, 0.12)', // Modals, dropdowns
        inner: 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.06)',
      },

      /* ===== 📏 SPACING SYSTEM ===== */
      spacing: {
        0.5: '0.125rem', // 2px
        1.5: '0.375rem', // 6px
        2.5: '0.625rem', // 10px
        3.5: '0.875rem', // 14px
        4.5: '1.125rem', // 18px
        5.5: '1.375rem', // 22px
        18: '4.5rem', // 72px
        22: '5.5rem', // 88px
        26: '6.5rem', // 104px
        30: '7.5rem', // 120px
      },

      /* ===== 🎭 OPACITY SCALES ===== */
      opacity: {
        35: '0.35',
        56: '0.56',
      },

      /* ===== 📐 WIDTH/HEIGHT UTILITIES ===== */
      maxWidth: {
        form: '600px', // Max width for forms
        content: '1200px', // Max width for main content
        sidebar: '280px', // Sidebar width
      },

      minHeight: {
        screen: '100vh',
        button: '44px', // Minimum touch target (accessibility)
      },

      /* ===== 🎬 ANIMATION & TRANSITIONS ===== */
      transitionProperty: {
        height: 'height',
        spacing: 'margin, padding',
      },

      transitionDuration: {
        250: '250ms',
        400: '400ms',
      },

      transitionTimingFunction: {
        'ease-in-out-cubic': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },

      /* ===== 🔢 Z-INDEX SCALE ===== */
      zIndex: {
        dropdown: '1000',
        sticky: '1020',
        fixed: '1030',
        modal: '1040',
        popover: '1050',
        tooltip: '1060',
      },

      /* ===== 🎯 CUSTOM UTILITIES ===== */
      strokeWidth: {
        0.5: '0.5px',
        1: '1px',
        4: '4px',
      },
    },
  },

  /* ===== 🔌 PLUGINS ===== */
  plugins: [
    tailwindcssAnimate,

    // Custom plugin for component classes
    function ({ addComponents, theme }) {
      addComponents({
        // Button Components
        '.btn-primary': {
          backgroundColor: theme('colors.primary.DEFAULT'),
          color: theme('colors.background.DEFAULT'),
          fontWeight: theme('fontWeight.bold'),
          borderRadius: theme('borderRadius.DEFAULT'),
          padding: `${theme('spacing.2')} ${theme('spacing.6')}`,
          transition: 'all 0.2s ease',
          '&:hover': {
            backgroundColor: theme('colors.primary.hover'),
          },
        },
        '.btn-secondary': {
          backgroundColor: theme('colors.secondary.DEFAULT'),
          color: theme('colors.background.DEFAULT'),
          fontWeight: theme('fontWeight.bold'),
          borderRadius: theme('borderRadius.DEFAULT'),
          padding: `${theme('spacing.2')} ${theme('spacing.6')}`,
          transition: 'all 0.2s ease',
          '&:hover': {
            backgroundColor: theme('colors.secondary.hover'),
          },
        },
        '.btn-danger': {
          backgroundColor: theme('colors.danger.DEFAULT'),
          color: theme('colors.background.DEFAULT'),
          fontWeight: theme('fontWeight.bold'),
          borderRadius: theme('borderRadius.DEFAULT'),
          padding: `${theme('spacing.2')} ${theme('spacing.6')}`,
          transition: 'all 0.2s ease',
          '&:hover': {
            backgroundColor: theme('colors.danger.hover'),
          },
        },

        // Input Components
        '.input-field': {
          backgroundColor: theme('colors.background.DEFAULT'),
          border: `1px solid ${theme('colors.border.DEFAULT')}`,
          borderRadius: theme('borderRadius.DEFAULT'),
          padding: `${theme('spacing.2')} ${theme('spacing.4')}`,
          fontSize: theme('fontSize.body-md[0]'),
          color: theme('colors.neutral.900'),
          '&:focus': {
            outline: 'none',
            borderColor: theme('colors.primary.DEFAULT'),
            boxShadow: `0 0 0 3px ${theme('colors.primary.hover')}`,
          },
          '&::placeholder': {
            color: theme('colors.neutral.400'),
          },
        },

        // Card Components
        '.card': {
          backgroundColor: theme('colors.background.DEFAULT'),
          borderRadius: theme('borderRadius.lg'),
          padding: theme('spacing.6'),
          boxShadow: theme('boxShadow.md'),
        },

        // Toast Notification
        '.toast': {
          backgroundColor: theme('colors.background.DEFAULT'),
          border: `0.5px solid ${theme('colors.border.dark')}`,
          borderRadius: theme('borderRadius.xl'),
          padding: theme('spacing.4'),
          boxShadow: theme('boxShadow.lg'),
        },
      })
    },
  ],
}
