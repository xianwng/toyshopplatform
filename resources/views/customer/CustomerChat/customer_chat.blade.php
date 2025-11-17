@extends('customer.layouts.cmaster')

@section('title', 'Chat - Toyspace')

@section('styles')
<style>
    :root {
        --primary-color: #3b82f6;
        --primary-light: #60a5fa;
        --primary-dark: #2563eb;
        --secondary-color: #10b981;
        --accent-color: #f59e0b;
        --warning-color: #f59e0b;
        --bg-color: #f8fafc;
        --card-bg: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --border-color: #e2e8f0;
        --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-light: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius: 12px;
        --radius-lg: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .chat-container {
        max-width: 1200px;
        margin: 80px auto 20px;
        padding: 0;
        background: var(--bg-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        overflow: hidden;
        height: calc(100vh - 100px);
    }

    .chat-header {
        background: linear-gradient(135deg, #000000, #333333);
        color: white;
        padding: 20px 30px;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .chat-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .chat-header-content {
        position: relative;
        z-index: 1;
    }

    .chat-header h1 {
        margin: 0 0 8px 0;
        font-size: 2rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chat-header p {
        margin: 0;
        opacity: 0.95;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .chat-main {
        display: flex;
        height: calc(100% - 80px);
        background: var(--card-bg);
    }

    .conversations-sidebar {
        flex: 0 0 380px;
        background: var(--card-bg);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .conversations-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .conversations-header h4 {
        margin: 0;
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1.1rem;
    }

    .conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px 0;
        min-height: 0;
    }

    .conversation-item {
        padding: 14px 20px;
        border-left: 4px solid transparent;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .conversation-item:hover {
        background: rgba(59, 130, 246, 0.05);
    }

    .conversation-item.active {
        background: rgba(59, 130, 246, 0.08);
        border-left-color: var(--primary-color);
    }

    .conversation-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .conversation-content {
        flex: 1;
        min-width: 0;
    }

    .conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 6px;
    }

    .conversation-seller {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.95rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .conversation-time {
        color: var(--text-muted);
        font-size: 0.75rem;
        flex-shrink: 0;
        margin-left: 8px;
    }

    .conversation-product {
        color: var(--text-secondary);
        font-size: 0.85rem;
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conversation-preview {
        color: var(--text-muted);
        font-size: 0.85rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .unread-indicator {
        position: absolute;
        top: 18px;
        right: 18px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--primary-color);
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 0;
    }

    .chat-header-bar {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 14px;
        background: var(--card-bg);
        flex-shrink: 0;
    }

    .chat-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .chat-details {
        flex: 1;
        min-width: 0;
        overflow: visible !important;
    }

    .chat-details h4 {
        margin: 0 0 4px 0;
        color: var(--text-primary);
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: flex-start;
        overflow: visible !important;
        position: relative;
        z-index: 1;
    }

    .chat-details p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .chat-status {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--secondary-color);
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        gap: 14px;
        min-height: 0;
    }

    /* IMPROVED MESSAGE STYLING */
    .message {
        margin-bottom: 6px;
        padding: 14px 18px;
        border-radius: 18px;
        max-width: 75%;
        word-wrap: break-word;
        position: relative;
        box-shadow: var(--shadow-light);
        animation: fadeIn 0.3s ease-out;
        line-height: 1.5;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message.customer {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 6px;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .message.shop {
        background: white;
        color: var(--text-primary);
        margin-right: auto;
        border-bottom-left-radius: 6px;
        border: 1px solid var(--border-color);
        font-size: 1.05rem;
        font-weight: 500;
    }

    /* ULTRA IMPROVED SYSTEM MESSAGES - BOLDER & BIGGER */
    .message.system {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin: 18px auto;
        text-align: center;
        max-width: 90%;
        border-radius: 20px;
        font-size: 1.2rem;
        font-weight: 700;
        padding: 22px 26px;
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        border: 3px solid rgba(255, 255, 255, 0.3);
        line-height: 1.6;
    }

    .message.auction-win {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        margin: 18px auto;
        text-align: center;
        max-width: 90%;
        border-radius: 20px;
        font-size: 1.2rem;
        font-weight: 700;
        padding: 22px 26px;
        box-shadow: 0 12px 35px rgba(245, 87, 108, 0.4);
        border: 3px solid rgba(255, 255, 255, 0.3);
        line-height: 1.6;
    }

    /* ULTRA IMPROVED BUY NOW MESSAGE STYLING - BOLDER & BIGGER */
    .message.buy-now {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        margin: 22px auto;
        text-align: left;
        max-width: 92%;
        border-radius: 22px;
        font-size: 1.3rem;
        font-weight: 800;
        padding: 26px 30px;
        box-shadow: 0 15px 40px rgba(79, 172, 254, 0.5);
        border: 3px solid rgba(255, 255, 255, 0.4);
        line-height: 1.7;
    }

    /* ULTRA IMPROVED REGULAR CHAT MESSAGE STYLING - BOLDER & BIGGER */
    .message.regular-chat {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin: 18px auto;
        text-align: left;
        max-width: 85%;
        border-radius: 20px;
        font-size: 1.25rem;
        font-weight: 700;
        padding: 22px 26px;
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        border: 3px solid rgba(255, 255, 255, 0.3);
        line-height: 1.6;
    }

    /* NEW: TRADE PROPOSAL MESSAGE STYLING */
    .message.trade-proposal {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
        margin: 18px auto;
        text-align: left;
        max-width: 90%;
        border-radius: 20px;
        font-size: 1.25rem;
        font-weight: 700;
        padding: 22px 26px;
        box-shadow: 0 12px 35px rgba(255, 107, 107, 0.4);
        border: 3px solid rgba(255, 255, 255, 0.3);
        line-height: 1.6;
    }

    /* NEW: TRADE COMPLETED MESSAGE STYLING */
    .message.trade-completed {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        margin: 18px auto;
        text-align: center;
        max-width: 90%;
        border-radius: 20px;
        font-size: 1.25rem;
        font-weight: 700;
        padding: 22px 26px;
        box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
        border: 3px solid rgba(255, 255, 255, 0.3);
        line-height: 1.6;
    }

    /* NEW: TRADE PROPOSAL IMAGE STYLING */
    .trade-proposal-image {
        margin: 10px 0;
        text-align: center;
    }

    .trade-proposal-image .message-image img {
        max-width: 280px;
        border-radius: 15px;
        border: 3px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .trade-proposal-image .message-image img:hover {
        transform: scale(1.03);
        border-color: rgba(255, 255, 255, 0.4);
    }

    /* INFORMATION REQUEST MESSAGE STYLING - BOLD & PROMINENT */
    .message.info-request {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        color: white;
        margin: 18px auto;
        text-align: center;
        max-width: 88%;
        border-radius: 20px;
        font-size: 1.3rem;
        font-weight: 800;
        padding: 24px 28px;
        box-shadow: 0 15px 40px rgba(255, 107, 107, 0.5);
        border: 3px solid rgba(255, 255, 255, 0.4);
        line-height: 1.7;
        cursor: pointer;
        transition: var(--transition);
    }

    .message.info-request:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 50px rgba(255, 107, 107, 0.6);
    }

    .message-time {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-top: 10px;
        display: block;
        font-weight: 600;
    }

    .message.customer .message-time {
        text-align: right;
        color: rgba(255,255,255,0.9);
    }

    .message.system .message-time,
    .message.auction-win .message-time,
    .message.buy-now .message-time,
    .message.regular-chat .message-time,
    .message.trade-proposal .message-time,
    .message.trade-completed .message-time,
    .message.info-request .message-time {
        text-align: center;
        color: rgba(255,255,255,0.9);
        font-size: 0.95rem;
        margin-top: 12px;
        font-weight: 600;
    }

    /* IMPROVED MESSAGE CONTENT STYLING - BIGGER & BOLDER */
    .message-text {
        font-size: 1.1rem;
        line-height: 1.6;
        font-weight: 500;
    }

    .message.system .message-text,
    .message.auction-win .message-text,
    .message.buy-now .message-text,
    .message.regular-chat .message-text,
    .message.trade-proposal .message-text,
    .message.trade-completed .message-text,
    .message.info-request .message-text {
        font-size: 1.3rem;
        text-align: left;
        font-weight: 700;
    }

    .message.customer .message-text {
        font-size: 1.15rem;
        font-weight: 600;
    }

    .message.shop .message-text {
        font-size: 1.05rem;
        font-weight: 500;
    }

    /* ULTRA IMPROVED BUY NOW SPECIFIC STYLING */
    .buy-now-header {
        text-align: center;
        margin-bottom: 22px;
        font-size: 1.5rem;
        font-weight: 800;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .buy-now-details {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 20px;
        margin: 18px 0;
        border: 2px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
    }

    .buy-now-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 2px solid rgba(255, 255, 255, 0.15);
        font-size: 1.2rem;
        font-weight: 600;
    }

    .buy-now-row:last-child {
        border-bottom: none;
    }

    .buy-now-row.total {
        font-weight: 800;
        font-size: 1.4rem;
        color: #fff;
        background: rgba(255, 255, 255, 0.15);
        margin: 12px -20px -20px -20px;
        padding: 16px 20px;
        border-radius: 0 0 12px 12px;
        border-top: 3px solid rgba(255, 255, 255, 0.25);
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .buy-now-label {
        font-weight: 700;
        color: rgba(255, 255, 255, 0.95);
        font-size: 1.2rem;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .buy-now-value {
        font-weight: 800;
        color: white;
        font-size: 1.3rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .buy-now-footer {
        text-align: center;
        margin-top: 22px;
        font-size: 1.2rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.98);
        padding-top: 16px;
        border-top: 2px solid rgba(255, 255, 255, 0.25);
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .no-chat-selected {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-secondary);
        text-align: center;
        padding: 40px 20px;
    }

    .no-chat-content {
        max-width: 300px;
    }

    .no-chat-icon {
        font-size: 3.5rem;
        color: var(--border-color);
        margin-bottom: 16px;
    }

    .chat-input-area {
        padding: 16px 20px;
        border-top: 1px solid var(--border-color);
        background: var(--card-bg);
        flex-shrink: 0;
    }

    .input-group {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .chat-input {
        flex: 1;
        padding: 14px 18px;
        border: 1px solid var(--border-color);
        border-radius: 24px;
        outline: none;
        transition: var(--transition);
        font-size: 1.05rem;
        font-weight: 500;
        resize: none;
        max-height: 120px;
        min-height: 48px;
        background: #f9fafb;
        line-height: 1.5;
    }

    .chat-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .image-upload-btn {
        background: var(--card-bg);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
    }

    .image-upload-btn:hover:not(:disabled) {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }

    .image-upload-btn:disabled {
        background: #f1f5f9;
        color: #cbd5e1;
        cursor: not-allowed;
    }

    .image-upload-btn input {
        display: none;
    }

    .send-btn {
        padding: 12px 20px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        border: none;
        border-radius: 24px;
        cursor: pointer;
        transition: var(--transition);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        font-size: 1.05rem;
    }

    .send-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 10px -1px rgba(59, 130, 246, 0.4);
    }

    .send-btn:disabled {
        background: #cbd5e1;
        cursor: not-allowed;
        box-shadow: none;
    }

    .message-image img {
        max-width: 260px;
        border-radius: 12px;
        cursor: pointer;
        transition: var(--transition);
    }

    .message-image img:hover {
        transform: scale(1.02);
    }

    .empty-conversations {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 3rem;
        color: var(--border-color);
        margin-bottom: 16px;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top: 3px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* IMPROVED Buy Now Order Summary */
    .buy-now-summary {
        margin-bottom: 25px;
    }

    .order-card {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        border: 3px solid var(--accent-color);
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 12px 35px rgba(245, 158, 11, 0.3);
    }

    .auction-win-card {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        border: 3px solid var(--secondary-color);
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 12px 35px rgba(40, 167, 69, 0.3);
    }

    .order-card h5 {
        color: #92400e;
        margin-bottom: 18px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.4rem;
    }

    .auction-win-card h5 {
        color: #155724;
        margin-bottom: 18px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.4rem;
    }

    .order-details {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 18px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .order-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 2px solid #f0f0f0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .order-row:last-child {
        border-bottom: none;
    }

    .order-row.total {
        font-weight: 800;
        font-size: 1.3rem;
        color: var(--secondary-color);
        background: #f8f9fa;
        margin: 10px -20px -20px -20px;
        padding: 16px 20px;
        border-radius: 0 0 12px 12px;
        border-top: 3px solid #e9ecef;
    }

    .order-row .label {
        color: var(--text-secondary);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .order-row .value {
        color: var(--text-primary);
        font-weight: 800;
        font-size: 1.2rem;
    }

    /* FIXED BADGE STYLES - COMPLETELY VISIBLE */
    .badge {
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        flex-shrink: 0;
        line-height: 1;
        height: auto;
        min-width: fit-content;
        position: relative;
        z-index: 10;
        overflow: visible !important;
        visibility: visible !important;
    }

    .badge.bg-primary {
        background: #dc2626 !important;
        color: white !important;
        border: none;
    }

    .badge.bg-warning {
        background: var(--warning-color) !important;
        color: #000 !important;
        border: none;
    }

    .badge.bg-success {
        background: var(--secondary-color) !important;
        color: white !important;
        border: none;
    }

    .badge.bg-info {
        background: #17a2b8 !important;
        color: white !important;
        border: none;
    }

    .badge.bg-danger {
        background: #dc3545 !important;
        color: white !important;
        border: none;
    }

    .badge.bg-secondary {
        background: #6c757d !important;
        color: white !important;
        border: none;
    }

    /* Item Source Badge Styles */
    .badge-sm {
        padding: 5px 10px;
        font-size: 0.7rem;
    }

    .badge-lg {
        padding: 8px 14px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .item-source-indicator {
        text-align: center;
        margin-bottom: 22px;
        padding: 14px;
    }

    .item-source-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        color: #495057;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .item-source-badge i {
        font-size: 0.9rem;
    }

    /* Item Received Section Styles */
    .item-received-section {
        margin: 22px 0;
        padding: 22px;
        border-radius: 18px;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 4px solid #28a745;
        box-shadow: 0 12px 35px rgba(40, 167, 69, 0.25);
    }

    .item-received-section .alert {
        margin: 0;
        border: none;
        background: transparent;
    }

    .item-received-section h5 {
        color: #155724;
        margin-bottom: 14px;
        font-weight: 800;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .item-received-section p {
        color: #155724;
        margin-bottom: 20px;
        font-size: 1.1rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .item-received-section .btn {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        padding: 14px 28px;
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
        border-radius: 15px;
    }

    .item-received-section .btn:hover {
        background: linear-gradient(135deg, #218838, #1ba87e);
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(40, 167, 69, 0.6);
    }

    /* NEW: Trade Complete Section Styles */
    .trade-complete-section {
        margin: 22px 0;
        padding: 22px;
        border-radius: 18px;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 4px solid #28a745;
        box-shadow: 0 12px 35px rgba(40, 167, 69, 0.25);
    }

    .trade-complete-section .alert {
        margin: 0;
        border: none;
        background: transparent;
    }

    .trade-complete-section h5 {
        color: #155724;
        margin-bottom: 14px;
        font-weight: 800;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .trade-complete-section p {
        color: #155724;
        margin-bottom: 20px;
        font-size: 1.1rem;
        line-height: 1.6;
        font-weight: 600;
    }

    .trade-complete-section .btn {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        padding: 14px 28px;
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
        border-radius: 15px;
    }

    .trade-complete-section .btn:hover {
        background: linear-gradient(135deg, #218838, #1ba87e);
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(40, 167, 69, 0.6);
    }

    /* NEW: Information Request Popup Styles */
    .info-request-popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(5px);
    }

    .info-request-modal {
        background: white;
        border-radius: 20px;
        padding: 26px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-50px) scale(0.9); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .info-request-header {
        text-align: center;
        margin-bottom: 22px;
    }

    .info-request-header h3 {
        color: var(--text-primary);
        font-size: 1.7rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .info-request-header p {
        color: var(--text-secondary);
        font-size: 1.05rem;
        font-weight: 500;
    }

    .info-request-textarea {
        width: 100%;
        min-height: 140px;
        padding: 18px;
        border: 3px solid var(--border-color);
        border-radius: 15px;
        font-size: 1.15rem;
        font-weight: 600;
        resize: vertical;
        margin-bottom: 22px;
        transition: var(--transition);
        background: #f9fafb;
    }

    .info-request-textarea:focus {
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .info-request-buttons {
        display: flex;
        gap: 14px;
        justify-content: flex-end;
    }

    .info-request-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 12px;
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
    }

    .info-request-btn.cancel {
        background: #f1f5f9;
        color: var(--text-secondary);
    }

    .info-request-btn.cancel:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .info-request-btn.send {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }

    .info-request-btn.send:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
    }

    /* FIXED: Ensure conversation badges display properly */
    .conversation-seller .badge {
        margin-right: 8px;
        flex-shrink: 0;
    }

    /* FIXED: Chat header badge styling - COMPLETELY VISIBLE */
    .chat-header-badge {
        display: inline-flex;
        align-items: center;
        margin-bottom: 4px;
        margin-left: 0;
        flex-shrink: 0;
        position: relative;
        z-index: 10;
        overflow: visible !important;
    }

    .username-display {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        line-height: 1.3;
        position: relative;
        z-index: 1;
    }

    .badge-container {
        display: flex;
        align-items: center;
        margin-bottom: 2px;
        flex-shrink: 0;
        position: relative;
        z-index: 10;
        overflow: visible !important;
    }

    /* FIXED: Improved header layout to prevent cutting */
    .chat-header-content-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        overflow: visible !important;
        position: relative;
        z-index: 1;
    }

    .chat-header-main {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 14px;
        overflow: visible !important;
    }

    .chat-header-text {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        overflow: visible !important;
        position: relative;
        z-index: 1;
    }

    /* FIXED: Ensure no overflow constraints */
    .chat-area,
    .chat-header-bar,
    .chat-details,
    .chat-details h4,
    .badge-container,
    .badge {
        overflow: visible !important;
    }

    /* Scrollbar Styling */
    .conversations-list::-webkit-scrollbar,
    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .conversations-list::-webkit-scrollbar-track,
    .chat-messages::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .conversations-list::-webkit-scrollbar-thumb,
    .chat-messages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .conversations-list::-webkit-scrollbar-thumb:hover,
    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    @media (max-width: 768px) {
        .chat-container {
            margin: 60px 10px 20px;
            border-radius: 12px;
            height: calc(100vh - 80px);
        }
        
        .chat-main {
            flex-direction: column;
            height: calc(100% - 70px);
        }
        
        .conversations-sidebar {
            flex: none;
            max-height: 250px;
        }
        
        .message {
            max-width: 90%;
            padding: 12px 16px;
        }
        
        .message.system,
        .message.auction-win,
        .message.buy-now,
        .message.regular-chat,
        .message.trade-proposal,
        .message.trade-completed,
        .message.info-request {
            max-width: 95%;
            padding: 18px 22px;
            font-size: 1.1rem;
        }
        
        .chat-header {
            padding: 16px 20px;
        }
        
        .conversation-item {
            padding: 12px 16px;
        }

        .buy-now-details {
            padding: 16px;
        }

        .buy-now-row {
            padding: 10px 0;
            font-size: 1.1rem;
        }

        .order-details {
            padding: 16px;
        }

        .order-row {
            padding: 10px 0;
            font-size: 1.05rem;
        }

        .buy-now-header {
            font-size: 1.3rem;
        }

        .buy-now-value {
            font-size: 1.1rem;
        }

        .info-request-modal {
            padding: 20px;
            margin: 20px;
        }

        .info-request-buttons {
            flex-direction: column;
        }

        .info-request-btn {
            width: 100%;
        }

        .badge {
            padding: 5px 10px;
            font-size: 0.7rem;
        }

        .username-display {
            font-size: 1rem;
        }

        .chat-header-bar {
            padding: 14px 16px;
            gap: 12px;
        }
        
        .chat-input-area {
            padding: 14px 16px;
        }
    }
</style>
@endsection

@section('content')
<div class="chat-container">
    <div class="chat-header">
        <div class="chat-header-content">
            <h1><i class="fa fa-comments me-2"></i> Messages</h1>
            <p>Private conversations with buyers and sellers</p>
        </div>
    </div>

    <div class="chat-main">
        <!-- Conversations Sidebar -->
        <div class="conversations-sidebar">
            <div class="conversations-header">
                <h4>Conversations</h4>
            </div>
            <div class="conversations-list" id="conversationsList">
                <div class="empty-conversations">
                    <div class="empty-icon">
                        <i class="fa fa-comments"></i>
                    </div>
                    <h4>No conversations yet</h4>
                    <p>Start a conversation by clicking "Chat" on a product or auction</p>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div class="chat-header-bar">
                <div class="chat-avatar" id="chatAvatar">
                    <i class="fa fa-user"></i>
                </div>
                <div class="chat-details">
                    <div class="chat-header-content-wrapper">
                        <div class="chat-header-text">
                            <h4 id="sellerName">
                                <div class="badge-container"></div>
                                <span class="username-display">Select a conversation</span>
                            </h4>
                            <p id="productName">Choose a conversation to start chatting</p>
                        </div>
                    </div>
                </div>
                <div class="chat-status" id="chatStatus">
                    <div class="status-dot"></div>
                    <span>Online</span>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="no-chat-selected" id="noChatSelected">
                    <div class="no-chat-content">
                        <div class="no-chat-icon">
                            <i class="fa fa-comments"></i>
                        </div>
                        <h3>Select a conversation</h3>
                        <p>Choose a conversation from the sidebar to start chatting</p>
                    </div>
                </div>
                <div id="conversationContainer"></div>
            </div>

            <div class="chat-input-area">
                <div class="input-group">
                    <div class="action-buttons">
                        <label for="imageUpload" class="image-upload-btn" title="Send image">
                            <i class="fa fa-image"></i>
                            <input type="file" id="imageUpload" accept="image/*" disabled>
                        </label>
                    </div>
                    
                    <textarea class="chat-input" id="messageInput" placeholder="Type your message..." rows="1" disabled></textarea>
                    <button class="send-btn" id="sendButton" disabled>
                        <i class="fa fa-paper-plane"></i> Send
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Information Request Popup -->
<div class="info-request-popup" id="infoRequestPopup" style="display: none;">
    <div class="info-request-modal">
        <div class="info-request-header">
            <h3>Ask for More Information</h3>
            <p>What would you like to know about this item?</p>
        </div>
        <textarea 
            class="info-request-textarea" 
            id="infoRequestText" 
            placeholder="I want to ask more information about this item you are selling..."
        ></textarea>
        <div class="info-request-buttons">
            <button class="info-request-btn cancel" id="cancelInfoRequest">Cancel</button>
            <button class="info-request-btn send" id="sendInfoRequest">Send Message</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentConversation = null;
    let messagePollInterval = null;

    // Initialize chat
    function initializeChat() {
        loadConversations();

        $(document).on('click', '.conversation-item', function() {
            selectConversation($(this).data('conversation'));
        });

        $('#sendButton').click(sendMessage);
        
        $('#messageInput').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            updateSendButtonState();
        });

        $('#messageInput').keypress(function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        $('#imageUpload').change(function() {
            if (this.files && this.files[0]) {
                uploadImage(this.files[0]);
            }
        });

        // Information Request Popup Handlers
        $(document).on('click', '.message.info-request', function() {
            showInfoRequestPopup();
        });

        $('#cancelInfoRequest').click(function() {
            hideInfoRequestPopup();
        });

        $('#sendInfoRequest').click(function() {
            sendInfoRequestMessage();
        });

        // Close popup when clicking outside
        $('#infoRequestPopup').click(function(e) {
            if (e.target === this) {
                hideInfoRequestPopup();
            }
        });

        startMessagePolling();
        
        // ✅ ENHANCED: Check for URL parameters and session conversations
        checkUrlParameters();
        checkSessionConversation();
    }

    // ✅ NEW: Check URL parameters for conversation ID
    function checkUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        const conversationParam = urlParams.get('conversation');
        
        if (conversationParam) {
            console.log('URL parameter conversation found:', conversationParam);
            // Store in session for persistence
            $.post('{{ route("customer.chat.find-or-create") }}', {
                _token: '{{ csrf_token() }}',
                conversation_id: conversationParam
            });
            
            // Select the conversation after a short delay
            setTimeout(() => {
                selectConversationFromParam(conversationParam);
            }, 1000);
        }
    }

    // ✅ NEW: Select conversation from URL parameter
    function selectConversationFromParam(conversationId) {
        const maxAttempts = 10;
        let attempts = 0;
        
        const trySelect = function() {
            attempts++;
            console.log(`Attempt ${attempts} to select conversation from URL:`, conversationId);
            
            const $conversationItem = $('.conversation-item[data-conversation="' + conversationId + '"]');
            
            if ($conversationItem.length > 0) {
                console.log('✅ Conversation from URL found, selecting:', conversationId);
                selectConversation(conversationId);
                
                // Clean URL without reload
                if (window.history.replaceState) {
                    const newUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                }
            } else if (attempts < maxAttempts) {
                console.log('Conversation from URL not found yet, retrying in', attempts * 300, 'ms');
                loadConversations();
                setTimeout(trySelect, attempts * 300);
            } else {
                console.log('❌ Failed to find conversation from URL after', maxAttempts, 'attempts');
            }
        };
        
        setTimeout(trySelect, 500);
    }

    function showInfoRequestPopup() {
        $('#infoRequestPopup').fadeIn(300);
        $('#infoRequestText').focus();
    }

    function hideInfoRequestPopup() {
        $('#infoRequestPopup').fadeOut(300);
        $('#infoRequestText').val('');
    }

    function sendInfoRequestMessage() {
        const messageText = $('#infoRequestText').val().trim();
        if (messageText && currentConversation) {
            $('#sendInfoRequest').prop('disabled', true).html('<div class="loading-spinner"></div> Sending...');
            
            $.ajax({
                url: '{{ route("customer.chat.send-message") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    conversation_id: currentConversation,
                    message: messageText
                },
                success: function(response) {
                    if (response.success) {
                        hideInfoRequestPopup();
                        loadConversationMessages(currentConversation);
                        loadConversations();
                    } else {
                        alert(response.message || 'Error sending message');
                    }
                    $('#sendInfoRequest').prop('disabled', false).html('Send Message');
                },
                error: function(xhr) {
                    let errorMessage = 'Error sending message. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    $('#sendInfoRequest').prop('disabled', false).html('Send Message');
                }
            });
        } else if (!messageText) {
            alert('Please enter your question about the item.');
            $('#infoRequestText').focus();
        }
    }

    // ✅ ENHANCED: Improved session conversation checking
    function checkSessionConversation() {
        const sessionConversationId = "{{ session('current_conversation_id') }}";
        const forceOpenChat = "{{ session('force_open_chat', false) }}";
        
        console.log('Session conversation check:', { 
            sessionConversationId, 
            forceOpenChat,
            hasSession: !!sessionConversationId 
        });
        
        if (sessionConversationId && sessionConversationId !== '') {
            // Try multiple times with increasing delays to ensure conversations are loaded
            const maxAttempts = 8;
            let attempts = 0;
            
            const trySelectConversation = function() {
                attempts++;
                console.log(`Attempt ${attempts} to select conversation:`, sessionConversationId);
                
                const $conversationItem = $('.conversation-item[data-conversation="' + sessionConversationId + '"]');
                
                if ($conversationItem.length > 0) {
                    console.log('✅ Conversation found, selecting:', sessionConversationId);
                    selectConversation(sessionConversationId);
                    
                    // Clear force open flag after successful selection
                    if (forceOpenChat) {
                        $.post('{{ route("customer.chat.clear-session") }}', {
                            _token: '{{ csrf_token() }}'
                        });
                    }
                } else if (attempts < maxAttempts) {
                    console.log('Conversation not found yet, retrying in', attempts * 400, 'ms');
                    // Load conversations again before retrying
                    loadConversations();
                    setTimeout(trySelectConversation, attempts * 400);
                } else {
                    console.log('❌ Failed to find conversation after', maxAttempts, 'attempts');
                    
                    // ✅ FIXED: Handle all chat types
                    if (sessionConversationId.startsWith('product_') || 
                        sessionConversationId.startsWith('auction_winner_') ||
                        sessionConversationId.startsWith('trade_proposal_')) {
                        console.log('Chat not found in storage - conversation may need to be recreated');
                        // Show error message to user
                        alert('Chat session not found. Please start a new conversation.');
                    }
                }
            };
            
            // Start trying after a short delay to ensure conversations are loaded
            setTimeout(trySelectConversation, 500);
        }
    }

    function loadConversations() {
        $.ajax({
            url: '{{ route("customer.chat.conversations") }}',
            type: 'GET',
            success: function(response) {
                $('#conversationsList').html(response.html);
            },
            error: function(xhr) {
                console.error('Error loading conversations:', xhr);
            }
        });
    }

    function selectConversation(conversationId) {
        $('.conversation-item').removeClass('active');
        $(`.conversation-item[data-conversation="${conversationId}"]`).addClass('active');
        
        loadConversationMessages(conversationId);
        
        // Enable input fields
        $('#messageInput').prop('disabled', false);
        $('#imageUpload').prop('disabled', false);
        updateSendButtonState();
        
        currentConversation = conversationId;
        
        updateCurrentConversation(conversationId);
        updateChatHeader(conversationId);
        $('#messageInput').focus();
    }

    function updateSendButtonState() {
        const messageText = $('#messageInput').val().trim();
        const hasMessage = messageText.length > 0;
        const hasConversation = currentConversation !== null;
        
        $('#sendButton').prop('disabled', !hasConversation || !hasMessage);
    }

    function updateCurrentConversation(conversationId) {
        $.ajax({
            url: '{{ route("customer.chat.find-or-create") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                conversation_id: conversationId
            },
            error: function(xhr) {
                console.error('Error updating current conversation:', xhr);
            }
        });
    }

    function loadConversationMessages(conversationId) {
        $.ajax({
            url: '{{ route("customer.chat.messages") }}',
            type: 'GET',
            data: { conversation_id: conversationId },
            beforeSend: function() {
                $('#conversationContainer').html('<div class="text-center p-3"><div class="loading-spinner"></div> Loading messages...</div>');
            },
            success: function(response) {
                $('#noChatSelected').hide();
                let $container = $('#conversationContainer');
                $container.html(response.html).show();
                scrollToBottom();
            },
            error: function(xhr) {
                console.error('Error loading messages:', xhr);
                $('#conversationContainer').html('<div class="text-center text-danger p-3">Error loading messages. Please try refreshing the page.</div>');
            }
        });
    }

    function updateChatHeader(conversationId) {
        const $selectedConversation = $(`.conversation-item[data-conversation="${conversationId}"]`);
        if ($selectedConversation.length > 0) {
            const personName = $selectedConversation.data('person-name');
            const productName = $selectedConversation.data('product-name');
            const chatType = $selectedConversation.data('chat-type');
            const itemSource = $selectedConversation.data('item-source');
            
            let typeBadge = getItemSourceBadge(itemSource, chatType);
            
            // Update the header with badge above username
            $('#sellerName').html(
                '<div class="badge-container">' + typeBadge + '</div>' +
                '<span class="username-display">' + personName + '</span>'
            );
            $('#productName').text(productName);
            
            if (personName) {
                $('#chatAvatar').text(personName.charAt(0).toUpperCase());
            }
        }
    }

    function getItemSourceBadge(itemSource, chatType) {
        let icon = '';
        let text = '';
        let color = '';
        
        switch (itemSource) {
            case 'product_management':
                icon = 'fa-box';
                text = 'Product';
                color = 'primary';
                break;
            case 'auction_management':
                icon = 'fa-gavel';
                text = 'Auction';
                color = 'warning';
                break;
            case 'trade_management':
                icon = 'fa-exchange-alt';
                text = 'Trade';
                color = 'info';
                break;
            default:
                icon = 'fa-question-circle';
                text = 'Unknown';
                color = 'secondary';
                break;
        }
        
        if (chatType === 'auction_winner') {
            icon = 'fa-trophy';
            text = 'Auction Win';
            color = 'success';
        } else if (chatType === 'buy_now') {
            icon = 'fa-bolt';
            text = 'Instant Buy';
            color = 'danger';
        } else if (chatType === 'trade_proposal') {
            icon = 'fa-exchange-alt';
            text = 'Trade Proposal';
            color = 'info';
        }
        
        return '<span class="badge bg-' + color + ' badge-sm chat-header-badge"><i class="fa ' + icon + ' me-1"></i>' + text + '</span>';
    }

    function sendMessage() {
        const messageText = $('#messageInput').val().trim();
        if (messageText && currentConversation) {
            
            $('#sendButton').prop('disabled', true).html('<div class="loading-spinner"></div>');
            $('#messageInput').prop('disabled', true);
            
            $.ajax({
                url: '{{ route("customer.chat.send-message") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    conversation_id: currentConversation,
                    message: messageText
                },
                success: function(response) {
                    if (response.success) {
                        $('#messageInput').val('').height('auto');
                        loadConversationMessages(currentConversation);
                        loadConversations();
                    } else {
                        alert(response.message || 'Error sending message');
                    }
                    $('#sendButton').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send');
                    $('#messageInput').prop('disabled', false);
                    updateSendButtonState();
                },
                error: function(xhr) {
                    let errorMessage = 'Error sending message. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    $('#sendButton').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send');
                    $('#messageInput').prop('disabled', false);
                    updateSendButtonState();
                }
            });
        }
    }

    function uploadImage(file) {
        if (file && currentConversation) {
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Image size should be less than 5MB.');
                return;
            }

            const formData = new FormData();
            formData.append('image', file);
            formData.append('conversation_id', currentConversation);
            formData.append('_token', '{{ csrf_token() }}');

            $('#imageUpload').prop('disabled', true);
            $('#sendButton').prop('disabled', true);
            $('#messageInput').prop('disabled', true);
            
            $.ajax({
                url: '{{ route("customer.chat.send-message") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        loadConversationMessages(currentConversation);
                        loadConversations();
                        $('#imageUpload').val('');
                    } else {
                        alert(response.message || 'Error uploading image');
                    }
                    $('#imageUpload').prop('disabled', false);
                    $('#sendButton').prop('disabled', false);
                    $('#messageInput').prop('disabled', false);
                    updateSendButtonState();
                },
                error: function(xhr) {
                    let errorMessage = 'Error uploading image. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    $('#imageUpload').prop('disabled', false);
                    $('#sendButton').prop('disabled', false);
                    $('#messageInput').prop('disabled', false);
                    updateSendButtonState();
                }
            });
        }
    }

    function startMessagePolling() {
        if (messagePollInterval) {
            clearInterval(messagePollInterval);
        }
        
        messagePollInterval = setInterval(function() {
            if (currentConversation) {
                checkForNewMessages();
            }
            loadConversations();
        }, 3000);
    }

    function checkForNewMessages() {
        if (!currentConversation) return;

        $.ajax({
            url: '{{ route("customer.chat.messages") }}',
            type: 'GET',
            data: {
                conversation_id: currentConversation,
                check_new: true
            },
            success: function(response) {
                const $container = $('#conversationContainer');
                if ($container.length > 0 && $container.is(':visible')) {
                    $container.html(response.html);
                    scrollToBottom();
                }
            },
            error: function(xhr) {
                console.error('Error checking for new messages:', xhr);
            }
        });
    }

    function scrollToBottom() {
        const chatMessages = $('#chatMessages');
        chatMessages.scrollTop(chatMessages[0].scrollHeight);
    }

    // Initialize the chat
    initializeChat();
    
    // Clean up on page unload
    $(window).on('beforeunload', function() {
        if (messagePollInterval) {
            clearInterval(messagePollInterval);
        }
    });
});

// Mark payment as received
function markPaymentReceived(conversationId) {
    const $conversationItem = $('.conversation-item[data-conversation="' + conversationId + '"]');
    const chatType = $conversationItem.data('chat-type');
    
    let confirmMessage = 'Are you sure you have received the payment? This will complete the transaction.';
    let successMessage = 'Payment confirmed successfully!';
    
    if (chatType === 'auction_winner') {
        confirmMessage = 'Are you sure you have delivered the item? This will complete the auction transaction.';
        successMessage = 'Delivery confirmed successfully!';
    }

    if (!confirm(confirmMessage)) {
        return;
    }

    $.ajax({
        url: '{{ route("customer.chat.mark-payment-received") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            conversation_id: conversationId
        },
        success: function(response) {
            if (response.success) {
                alert(successMessage);
                if (typeof loadConversationMessages === 'function') {
                    loadConversationMessages(conversationId);
                }
                if (typeof loadConversations === 'function') {
                    loadConversations();
                }
            } else {
                alert(response.message || 'Error confirming payment');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Error confirming payment. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
}

// Mark item as received for auction winners
function markItemReceived(conversationId, auctionId) {
    if (confirm('Are you sure you have received the item? This will AUTOMATICALLY release the payment to the seller without admin approval.')) {
        $.ajax({
            url: '/customer/auctions/' + auctionId + '/mark-received',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                conversation_id: conversationId
            },
            success: function(response) {
                alert(response.success);
                if (typeof loadConversationMessages === 'function') {
                    loadConversationMessages(conversationId);
                }
                if (typeof loadConversations === 'function') {
                    loadConversations();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON.error || 'Failed to mark item as received');
            }
        });
    }
}

// Complete Trade Function
function completeTrade(conversationId, tradeId) {
    if (confirm('Are you sure you want to mark this trade as completed? This will end the exchange and change the trade status from "Active" to "Completed".')) {
        $.ajax({
            url: '/trading/' + tradeId + '/complete',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                conversation_id: conversationId
            },
            success: function(response) {
                if (response.success) {
                    alert('Trade marked as completed successfully! The trade status has been updated.');
                    if (typeof loadConversationMessages === 'function') {
                        loadConversationMessages(conversationId);
                    }
                    if (typeof loadConversations === 'function') {
                        loadConversations();
                    }
                } else {
                    alert(response.message || 'Error completing trade');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error completing trade. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            }
        });
    }
}

// Open image modal
function openImageModal(imageUrl) {
    window.open(imageUrl, '_blank');
}
</script>
@endsection