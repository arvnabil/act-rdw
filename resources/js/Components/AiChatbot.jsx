import React, { useState, useRef, useEffect } from 'react';
import axios from 'axios';
import { getWhatsAppLink } from '@/Utils/whatsapp';

const aiAgent = {
    name: 'Vion Assistant',
    role: 'ICT Solutions Consultant',
    avatar: '/images/vion.png',
    headerAvatar: '/images/avatar-vion.png',
    color: '#059669',
    gradient: 'linear-gradient(135deg, #ffffff, #f1f5f9)',
    actionGradient: 'linear-gradient(135deg, #10b981, #059669)',
};

// const WA_NUMBER = '6281280944719'; // Now dynamic via botSettings.whatsapp_number

const formatMarkdown = (text) => {
    if (!text) return '';
    
    // Pre-processing:
    // 1. Fix cases where list numbers are joined to the previous sentence (e.g., "...consideration: 1.")
    let processedText = text.replace(/([.!?])\s*(\d+\.)/g, '$1\n$2');
    
    // 2. Fix cases where bullet points are joined to the previous sentence (e.g., "...Anda: * Solusi")
    processedText = processedText.replace(/([:!?.])\s*([\*•\-])\s+/g, '$1\n$2 ');
    
    // 3. Fix cases where closing remarks or follow-up questions are joined at the end of a bullet point line
    processedText = processedText.replace(/((?:[\*•\-]\s+|\d+\.\s+)(?:\*\*.*?\*\*|[^:\n]+):?.+?[.!?])\s*(Jika Anda|Apakah Anda|Apakah ada|Masing-masing|Jangan ragu|Silakan|Hubungi|Ada yang|Bagaimana|Untuk informasi)/gi, '$1\n\n$2');
    
    // Pre-processing: Clean up common AI hallucinations and normalize newlines
    processedText = processedText
        .replace(/<br\s*\/?>/gi, '\n') // Replace <br> tags with actual newlines
        .replace(/\n{3,}/g, '\n\n')    // Normalize excessive newlines
        .trim();
    
    // 3. handle horizontal rules
    processedText = processedText.replace(/^---$/gm, '<hr />');
    
    const lines = processedText.split('\n');
    const result = [];
    let currentTable = null;

    for (let i = 0; i < lines.length; i++) {
        const line = lines[i].trim();
        
        // Handle Horizontal Rule
        if (line === '<hr />') {
            result.push(<hr key={i} style={{ border: 'none', borderTop: '1px solid rgba(255,255,255,0.1)', margin: '15px 0' }} />);
            continue;
        }

        // Improved Table Detection
        if (line.includes('|') && line.indexOf('|') !== line.lastIndexOf('|')) {
            const firstPipeIndex = line.indexOf('|');
            const lastPipeIndex = line.lastIndexOf('|');
            
            const textBefore = line.substring(0, firstPipeIndex).trim();
            const tablePart = line.substring(firstPipeIndex, lastPipeIndex + 1);
            const extraText = line.substring(lastPipeIndex + 1).trim();

            if (textBefore) {
                result.push(<div key={`text-before-${i}`} style={{ marginBottom: '8px' }}>{parseInlineFormatting(textBefore)}</div>);
            }

            if (!currentTable) {
                currentTable = { header: null, rows: [] };
            }
            
            const cells = tablePart.split('|').filter((c, idx, arr) => {
                // Keep cells that are between pipes, even if empty
                return idx > 0 && idx < arr.length - 1;
            }).map(c => c.trim());
            
            // Check if it's a separator line (|---|)
            if (tablePart.match(/^[|\s-:]+$/)) {
                // If there's extra text after a separator (unlikely but possible), handle it
                if (extraText) lines.splice(i + 1, 0, extraText); 
                continue; 
            }

            if (!currentTable.header) {
                currentTable.header = cells;
            } else {
                currentTable.rows.push(cells);
            }

            // If there's extra text after a row, push it to the next line to be processed
            if (extraText) {
                lines.splice(i + 1, 0, extraText);
            }

            // Check if next line is not a table line
            const nextLine = lines[i+1] ? lines[i+1].trim() : '';
            const nextIsTable = nextLine.includes('|') && nextLine.indexOf('|') !== nextLine.lastIndexOf('|');
            if (i === lines.length - 1 || !nextIsTable) {
                const tableKey = `table-${i}`;
                result.push(
                    <div key={tableKey} style={{ 
                        overflowX: 'auto', 
                        margin: '15px 0', 
                        borderRadius: '10px', 
                        border: '1px solid rgba(255,255,255,0.15)',
                        backgroundColor: 'rgba(0,0,0,0.2)',
                        width: '100%',
                        WebkitOverflowScrolling: 'touch' // Untuk scroll halus di iOS
                    }}>
                        <table style={{ 
                            minWidth: '500px', // Memaksa lebar minimal agar muncul scrollbar
                            width: '100%', 
                            borderCollapse: 'collapse', 
                            fontSize: '0.85em', 
                            color: '#fff',
                            tableLayout: 'auto'
                        }}>
                            <thead>
                                <tr style={{ background: 'rgba(255,255,255,0.1)' }}>
                                    {currentTable.header.map((h, idx) => (
                                        <th key={idx} style={{ 
                                            padding: '12px 15px', 
                                            textAlign: 'left', 
                                            borderBottom: '2px solid rgba(255,255,255,0.2)', 
                                            fontWeight: '800',
                                            whiteSpace: 'nowrap' // Mencegah judul kolom berantakan
                                        }}>
                                            {parseInlineFormatting(h)}
                                        </th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {currentTable.rows.map((row, rowIdx) => (
                                    <tr key={rowIdx} style={{ 
                                        borderBottom: '1px solid rgba(255,255,255,0.05)',
                                        backgroundColor: rowIdx % 2 === 0 ? 'transparent' : 'rgba(255,255,255,0.02)' // Zebra striping
                                    }}>
                                        {row.map((cell, cellIdx) => (
                                            <td key={cellIdx} style={{ 
                                                padding: '10px 15px', 
                                                opacity: 0.95,
                                                lineHeight: '1.4'
                                            }}>
                                                {parseInlineFormatting(cell)}
                                            </td>
                                        ))}
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                );
                currentTable = null;
            }
            continue;
        }

        if (!line) {
            result.push(<div key={i} style={{ height: '16px' }} />);
            continue;
        }
        
        // Handle Headings
        if (line.startsWith('### ')) {
            result.push(<h3 key={i} style={{ margin: '16px 0 8px 0', fontSize: '1.2em', fontWeight: '800', color: '#fff', borderLeft: `4px solid ${aiAgent.color}`, paddingLeft: '10px' }}>{parseInlineFormatting(line.slice(4))}</h3>);
            continue;
        }
        if (line.startsWith('#### ')) {
            result.push(<h4 key={i} style={{ margin: '14px 0 6px 0', fontSize: '1.05em', fontWeight: '700', color: '#fff', opacity: 0.9 }}>{parseInlineFormatting(line.slice(5))}</h4>);
            continue;
        }
        if (line.startsWith('## ')) {
            result.push(<h2 key={i} style={{ margin: '20px 0 10px 0', fontSize: '1.4em', fontWeight: '800', color: '#fff', borderBottom: '1px solid rgba(255,255,255,0.1)', paddingBottom: '4px' }}>{parseInlineFormatting(line.slice(3))}</h2>);
            continue;
        }
        
        // Handle Bullet Lists (* or -)
        if (line.trim().startsWith('* ') || line.trim().startsWith('- ')) {
            const content = line.trim().slice(2);
            result.push(
                <div key={i} style={{ display: 'flex', gap: '10px', marginBottom: '6px', paddingLeft: '8px' }}>
                    <span style={{ color: aiAgent.color, fontWeight: 'bold' }}>•</span>
                    <span style={{ flex: 1 }}>{parseInlineFormatting(content)}</span>
                </div>
            );
            continue;
        }

        // Handle Numbered Lists (1. )
        const numberedListMatch = line.trim().match(/^(\d+)\.\s+(.*)/);
        if (numberedListMatch) {
            result.push(
                <div key={i} style={{ display: 'flex', gap: '10px', marginBottom: '6px', paddingLeft: '8px' }}>
                    <span style={{ color: aiAgent.color, fontWeight: 'bold', fontSize: '0.9em' }}>{numberedListMatch[1]}.</span>
                    <span style={{ flex: 1 }}>{parseInlineFormatting(numberedListMatch[2])}</span>
                </div>
            );
            continue;
        }

        result.push(<div key={i} style={{ marginBottom: '12px', lineHeight: '1.6', opacity: 0.98 }}>{parseInlineFormatting(line)}</div>);
    }
    return result;
};

const parseInlineFormatting = (text) => {
    if (typeof text !== 'string') return text;

    // Bold-Italic: ***text***
    let parts = [text];
    
    const rules = [
        { regex: /\*\*\*(.*?)\*\*\*/g, render: (m, p) => <strong key={m}><em>{p}</em></strong> },
        { regex: /\*\*(.*?)\*\*/g, render: (m, p) => <strong key={m} style={{ color: '#fff', fontWeight: '700' }}>{p}</strong> },
        { regex: /\*(.*?)\*/g, render: (m, p) => <em key={m}>{p}</em> },
        { regex: /__(.*?)__/g, render: (m, p) => <u key={m}>{p}</u> },
        { regex: /\[(.*?)\]\((.*?)\)/g, render: (m, label, url) => <a key={m} href={url} target="_blank" rel="noopener noreferrer" style={{ color: '#3b82f6', textDecoration: 'underline', fontWeight: '600' }}>{label}</a> },
        { regex: /`(.*?)`/g, render: (m, p) => <code key={m} style={{ background: 'rgba(255,255,255,0.1)', padding: '2px 4px', borderRadius: '4px', fontSize: '0.9em', fontFamily: 'monospace' }}>{p}</code> },
    ];

    let result = [text];

    rules.forEach(rule => {
        let newResult = [];
        result.forEach(part => {
            if (typeof part !== 'string') {
                newResult.push(part);
                return;
            }
            
            const matches = [...part.matchAll(rule.regex)];
            if (matches.length === 0) {
                newResult.push(part);
                return;
            }

            let lastIndex = 0;
            matches.forEach((match, idx) => {
                if (match.index > lastIndex) {
                    newResult.push(part.substring(lastIndex, match.index));
                }
                
                // Pass all capture groups to render function
                const args = match.slice(1);
                newResult.push(rule.render(`${rule.regex.toString()}-${idx}`, ...args));
                
                lastIndex = match.index + match[0].length;
            });
            
            if (lastIndex < part.length) {
                newResult.push(part.substring(lastIndex));
            }
        });
        result = newResult;
    });

    return result;
};


const ProductCard = ({ product }) => {
    const defaultImg = '/assets/default.png';
    const [isHovered, setIsHovered] = useState(false);
    return (
        <a href={product.link} target="_blank" rel="noopener noreferrer" style={{
            flexShrink: 0, width: '160px', background: 'rgba(255,255,255,0.05)', borderRadius: '15px', 
            border: '1px solid rgba(255,255,255,0.12)', overflow: 'hidden', backdropFilter: 'blur(15px)',
            display: 'flex', flexDirection: 'column', textDecoration: 'none', transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)', 
            cursor: 'pointer', boxShadow: '0 4px 12px rgba(0,0,0,0.1)'
        }} onMouseEnter={e => {
            setIsHovered(true);
            e.currentTarget.style.transform = 'translateY(-4px)';
            e.currentTarget.style.borderColor = 'rgba(255,255,255,0.25)';
        }}
           onMouseLeave={e => {
            setIsHovered(false);
            e.currentTarget.style.transform = 'translateY(0)';
            e.currentTarget.style.borderColor = 'rgba(255,255,255,0.12)';
        }}>
            <div style={{ width: '100%', height: '100px', background: '#fff', position: 'relative', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '8px' }}>
                <img 
                    src={product.image} 
                    onError={(e) => { e.target.onerror = null; e.target.src = defaultImg; }}
                    alt={product.name} 
                    style={{ maxWidth: '100%', maxHeight: '100%', objectFit: 'contain' }} 
                />
                {product.link_accommerce && (
                    <div style={{
                        position: 'absolute', top: 0, left: 0, width: '100%', height: '100%',
                        background: 'rgba(15, 23, 42, 0.65)', backdropFilter: 'blur(4px)',
                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                        opacity: isHovered ? 1 : 0, transition: 'all 0.3s ease',
                        pointerEvents: isHovered ? 'auto' : 'none', zIndex: 10
                    }}>
                        <button onClick={(e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            window.open(product.link_accommerce, '_blank');
                        }} style={{
                            background: 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                            color: '#fff', fontSize: '10px', fontWeight: '800',
                            padding: '6px 12px', borderRadius: '20px', textTransform: 'uppercase',
                            boxShadow: '0 4px 10px rgba(59, 130, 246, 0.4)', border: 'none',
                            cursor: 'pointer', display: 'inline-flex', alignItems: 'center', gap: '4px',
                            transition: 'transform 0.2s', outline: 'none'
                        }} onMouseEnter={e => e.currentTarget.style.transform = 'scale(1.05)'} onMouseLeave={e => e.currentTarget.style.transform = 'scale(1)'}>
                            🛒 Toko Online
                        </button>
                    </div>
                )}
            </div>
            <div style={{ padding: '10px 12px', display: 'flex', flexDirection: 'column', gap: '8px', flex: 1 }}>
                <div style={{ 
                    fontSize: '12px', color: '#fff', fontWeight: '600', 
                    height: '36px', overflow: 'hidden', display: '-webkit-box', 
                    WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', lineHeight: '1.4' 
                }}>
                    {product.name}
                </div>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 'auto' }}>
                    <div style={{ fontSize: '10px', color: 'rgba(255,255,255,0.5)', fontWeight: '500' }}>
                        Detail ↗
                    </div>
                    {product.qty && (
                        <div style={{ background: 'rgba(255,255,255,0.1)', color: '#fff', padding: '1px 6px', borderRadius: '6px', fontSize: '9px', fontWeight: 'bold' }}>
                            x{product.qty}
                        </div>
                    )}
                </div>
            </div>
        </a>
    );
};

const ProductList = ({ products }) => {
    const listRef = useRef(null);
    const isDown = useRef(false);
    const startX = useRef(0);
    const scrollLeft = useRef(0);

    useEffect(() => {
        const el = listRef.current;
        if (!el) return;

        const handleWheel = (e) => {
            if (e.deltaY !== 0) {
                e.preventDefault();
                e.stopPropagation();
                el.scrollLeft += e.deltaY;
            }
        };

        const handleMouseDown = (e) => {
            isDown.current = true;
            el.style.cursor = 'grabbing';
            startX.current = e.pageX - el.offsetLeft;
            scrollLeft.current = el.scrollLeft;
        };

        const handleMouseLeave = () => {
            isDown.current = false;
            el.style.cursor = 'grab';
        };

        const handleMouseUp = () => {
            isDown.current = false;
            el.style.cursor = 'grab';
        };

        const handleMouseMove = (e) => {
            if (!isDown.current) return;
            e.preventDefault();
            const x = e.pageX - el.offsetLeft;
            const walk = (x - startX.current) * 2; // Scroll speed
            el.scrollLeft = scrollLeft.current - walk;
        };

        el.addEventListener('wheel', handleWheel, { passive: false });
        el.addEventListener('mousedown', handleMouseDown);
        el.addEventListener('mouseleave', handleMouseLeave);
        el.addEventListener('mouseup', handleMouseUp);
        el.addEventListener('mousemove', handleMouseMove);

        return () => {
            el.removeEventListener('wheel', handleWheel);
            el.removeEventListener('mousedown', handleMouseDown);
            el.removeEventListener('mouseleave', handleMouseLeave);
            el.removeEventListener('mouseup', handleMouseUp);
            el.removeEventListener('mousemove', handleMouseMove);
        };
    }, []);

    return (
        <div 
            ref={listRef}
            className="product-list-container" 
            data-lenis-prevent
            style={{ 
                width: 'calc(100% + 32px)', 
                margin: '10px -16px 0 -16px', 
                display: 'flex', 
                flexDirection: 'row',
                flexWrap: 'nowrap',
                gap: '16px', 
                overflowX: 'auto', 
                padding: '8px 16px 16px 16px',
                scrollbarWidth: 'none',
                msOverflowStyle: 'none',
                WebkitOverflowScrolling: 'touch',
                cursor: 'grab',
                overscrollBehavior: 'none',
                userSelect: 'none'
            }}>
            {products.map((p, idx) => (
                <div key={idx} onDragStart={e => e.preventDefault()}>
                    <ProductCard product={p} />
                </div>
            ))}
        </div>
    );
};

const WhatsAppButton = ({ messages, waNumber }) => {
    const [isSummarizing, setIsSummarizing] = useState(false);
    const handleWaClick = async (e) => {
        e.preventDefault();
        setIsSummarizing(true);
        try {
            const { data } = await axios.post('/api/ai/summarize', { history: messages.slice(-10) });
            const text = data.summary || "Halo Tim Sales ACTiV, 👋\n\nSaya ingin diskusi lebih lanjut terkait prospek yang baru masuk.";
            const link = getWhatsAppLink(waNumber, {
                message: text,
                cta_position: 'vion_chatbot',
                cta_label: 'Chatbot AI Redirection'
            });
            window.open(link, '_blank');
        } catch (err) {
            const fallbackLink = getWhatsAppLink(waNumber, {
                message: "Halo Tim Sales ACTiV, 👋\n\nSaya ingin diskusi lebih lanjut terkait prospek yang baru masuk. (Gagal membuat rangkuman otomatis)",
                cta_position: 'vion_chatbot',
                cta_label: 'Chatbot AI Redirection (Fallback)'
            });
            window.open(fallbackLink, '_blank');
        } finally { setIsSummarizing(false); }
    };
    return (
        <button onClick={handleWaClick} disabled={isSummarizing} style={{
            display: 'inline-flex', alignItems: 'center', gap: '8px', background: '#25d366', color: '#fff', padding: '10px 16px',
            borderRadius: '12px', fontSize: '13px', fontWeight: '600', border: 'none', cursor: 'pointer', marginTop: '10px', width: '100%', justifyContent: 'center'
        }}>
            <span>{isSummarizing ? '⏳ Menyiapkan...' : '💬 Hubungi Tim Sales (WA)'}</span>
        </button>
    );
};

const getGreetingTime = () => {
    const hour = new Date().getHours();
    if (hour >= 4 && hour < 11) return 'Pagi';
    if (hour >= 11 && hour < 15) return 'Siang';
    if (hour >= 15 && hour < 19) return 'Sore';
    return 'Malam';
};

const LeadForm = ({ onComplete }) => {
    const [formData, setFormData] = useState({ name: '', whatsapp: '', email: '', company: '' });
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        try {
            const { data } = await axios.post('/api/ai/start-session', formData);
            if (data.success) { onComplete(data.session_id, formData); }
        } catch (err) {
            alert('Gagal memulai sesi. Silakan coba lagi.');
        } finally { setIsSubmitting(false); }
    };

    return (
        <div style={{ padding: '16px 20px', display: 'flex', flexDirection: 'column', gap: '12px', color: '#fff' }}>
            <div style={{ textAlign: 'center', marginBottom: '4px' }}>
                <h3 style={{ margin: 0, fontSize: '16px', fontWeight: '600' }}>Halo, Selamat {getGreetingTime()}! 👋</h3>
                <p style={{ margin: '3px 0 0', fontSize: '12px', opacity: 0.8 }}>Boleh tahu siapa yang kami ajak bicara?</p>
            </div>
            <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                <input required placeholder="Nama Lengkap" value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})}
                       style={{ background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '8px', padding: '0 12px', height: '40px', boxSizing: 'border-box', fontSize: '13px', color: '#fff', outline: 'none' }} />
                <input required placeholder="No. WhatsApp (Contoh: 0812...)" value={formData.whatsapp} onChange={e => setFormData({...formData, whatsapp: e.target.value})}
                       style={{ background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '8px', padding: '0 12px', height: '40px', boxSizing: 'border-box', fontSize: '13px', color: '#fff', outline: 'none' }} />
                <input type="email" placeholder="Email (Opsional)" value={formData.email} onChange={e => setFormData({...formData, email: e.target.value})}
                       style={{ background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '8px', padding: '0 12px', height: '40px', boxSizing: 'border-box', fontSize: '13px', color: '#fff', outline: 'none' }} />
                <input placeholder="Nama Perusahaan (Isi 'Personal' jika tidak ada)" value={formData.company} onChange={e => setFormData({...formData, company: e.target.value})}
                       style={{ background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '8px', padding: '0 12px', height: '40px', boxSizing: 'border-box', fontSize: '13px', color: '#fff', outline: 'none' }} />
                <button type="submit" disabled={isSubmitting} style={{
                    background: aiAgent.actionGradient, color: '#fff', border: 'none', borderRadius: '8px', height: '40px', boxSizing: 'border-box', fontSize: '13px', fontWeight: '700', cursor: 'pointer', marginTop: '4px', transition: 'opacity 0.2s', display: 'flex', alignItems: 'center', justifyContent: 'center', width: '100%'
                }}>
                    {isSubmitting ? '⏳ Menyiapkan...' : 'Mulai Konsultasi'}
                </button>
            </form>
        </div>
    );
};


const MessageBubble = ({ msg, allMessages, waNumber }) => {
    const isUser = msg.role === 'user';
    const hasTrigger = !isUser && (msg.content.includes('[HUBUNGI_SALES]') || msg.content.includes('[WA_TRIGGER]'));
    const cleanContent = !isUser ? msg.content.replace(/\[HUBUNGI_SALES\]|\[WA_TRIGGER\]/g, '').trim() : msg.content;

    return (
        <div style={{ display: 'flex', flexDirection: 'column', alignItems: isUser ? 'flex-end' : 'flex-start', marginBottom: '16px', width: '100%' }}>
            <div style={{ display: 'flex', justifyContent: isUser ? 'flex-end' : 'flex-start', gap: '8px', alignItems: 'flex-end', width: '100%' }}>
                {!isUser && (
                    <div style={{
                        width: '32px', height: '32px', borderRadius: '50%', background: `url(${aiAgent.headerAvatar}) center/cover no-repeat, #f1f5f9`,
                        flexShrink: 0, border: '1px solid rgba(0,0,0,0.05)',
                    }} />
                )}
                <div style={{
                    maxWidth: '80%', padding: '10px 14px', borderRadius: isUser ? '18px 18px 4px 18px' : '18px 18px 18px 4px',
                    background: isUser ? aiAgent.actionGradient : 'rgba(30, 41, 59, 0.75)', color: '#fff', fontSize: '14px', lineHeight: '1.6',
                    backdropFilter: 'blur(12px)', border: isUser ? 'none' : '1px solid rgba(255,255,255,0.1)', wordBreak: 'break-word',
                    boxShadow: '0 4px 15px rgba(0,0,0,0.1)',
                }}>
                    {formatMarkdown(cleanContent)}
                    {hasTrigger && <WhatsAppButton messages={allMessages} waNumber={waNumber} />}
                </div>
            </div>
            
            {/* Horizontal Product Recommendations */}
            {!isUser && msg.products && msg.products.length > 0 && (
                <ProductList products={msg.products} />
            )}

        </div>
    );
};


export default function AiChatbot({ serverSettings }) {
    const [isOpen, setIsOpen] = useState(() => localStorage.getItem('vion_chat_open') === 'true');
    const [sessionId, setSessionId] = useState(() => localStorage.getItem('vion_session_id'));
    const [userData, setUserData] = useState(null);
    const [messages, setMessages] = useState([]);
    const [input, setInput] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    // Persist window open state
    useEffect(() => {
        localStorage.setItem('vion_chat_open', isOpen);
    }, [isOpen]);

    // Use settings from server (Inertia shared props) for instant load
    const [botSettings, setBotSettings] = useState(() => {
        if (serverSettings) {
            try {
                return {
                    welcome_message: serverSettings.vion_welcome_message || '',
                    starter_buttons: JSON.parse(serverSettings.vion_starter_buttons || '[]'),
                    is_active: serverSettings.vion_is_active === '1',
                    whatsapp_number: serverSettings.vion_whatsapp_number || '6281280944719'
                };
            } catch (e) {
                console.error('Failed to parse starter buttons', e);
            }
        }
        return { 
            welcome_message: '', 
            starter_buttons: [], 
            is_active: true,
            whatsapp_number: '6281280944719'
        };
    });

    const messagesEndRef = useRef(null);
    const lastMessageRef = useRef(null);
    const inputRef = useRef(null);

    // Persistence: Load history if session exists
    useEffect(() => {
        const fetchHistory = async () => {
            if (!sessionId) return;
            try {
                const { data } = await axios.get(`/api/ai/get-history?session_id=${sessionId}`);
                if (data.messages) {
                    setMessages(data.messages);
                    if (data.session) setUserData(data.session); // PENTING: Pulihkan Nama User
                }
                
                // Jika sesi benar-benar tidak ditemukan (404/Empty) oleh server
                if (!data.session && (!data.messages || data.messages.length === 0)) {
                    localStorage.removeItem('vion_session_id');
                    setSessionId(null);
                }
            } catch (err) {
                // Handle session expiration
                if (err.response && (err.response.status === 403 || (err.response.data && err.response.data.status === 'expired'))) {
                    localStorage.removeItem('vion_session_id');
                    setSessionId(null);
                    setMessages([]);
                }
                console.error('Failed to fetch chat history');
            }
        };
        fetchHistory();
    }, [sessionId]);

    useEffect(() => {
        if (isOpen) {
            // Prevent body scroll if needed, but lenis-prevent should handle it
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "auto";
        }
        return () => { 
            document.body.style.overflow = 'unset'; 
        };
    }, [isOpen]);

    // Improved Scroll to bottom
    useEffect(() => { 
        if (messages.length > 0) {
            // Jika sedang loading (AI sedang mengetik), scroll ke paling bawah (end ref)
            if (isLoading) {
                messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
            } else {
                // Jika tidak sedang loading, scroll ke pesan terakhir
                // Gunakan 'instant' jika baru buka atau muat history agar tidak pusing melihat animasi scroll panjang
                const scrollBehavior = messages.length <= 2 ? 'smooth' : 'auto';
                lastMessageRef.current?.scrollIntoView({ behavior: scrollBehavior, block: 'start' }); 
            }
        }
    }, [messages, isLoading, isOpen]);

    const startChat = (id, data) => {
        setSessionId(id);
        localStorage.setItem('vion_session_id', id);
        setUserData(data);

        // Add initial welcome message from settings
        if (botSettings.welcome_message) {
            const welcome = botSettings.welcome_message.replace(/\[Nama\]/g, data.name);
            setMessages([{ role: 'assistant', content: welcome }]);
        }
    };

    const [showActionsAlways, setShowActionsAlways] = useState(false);

    const handleStarterClick = (btn) => {
        setShowActionsAlways(false); // Hide after selection
        if (btn.instant_response) {
            // Show user's "click" as a message
            setMessages(prev => [...prev, { role: 'user', content: btn.label }]);
            setIsLoading(true);
            
            // Show hardcoded response after a small "thinking" delay for realism
            setTimeout(() => {
                setMessages(prev => [...prev, { role: 'assistant', content: btn.instant_response }]);
                setIsLoading(false);
            }, 600);
        } else {
            setInput(btn.message);
            sendQuickMessage(btn.message);
        }
    };

    const sendQuickMessage = async (msg) => {
        if (isLoading || !sessionId) return;
        setMessages(prev => [...prev, { role: 'user', content: msg }]);
        setIsLoading(true);
        try {
            const { data } = await axios.post('/api/ai/chat', { message: msg, session_id: sessionId });
            setMessages(prev => [...prev, { role: 'assistant', content: data.response, products: data.products }]);
        } catch (err) {
            setMessages(prev => [...prev, { role: 'assistant', content: 'Maaf, saya sedang mengalami gangguan teknis. 🙏' }]);
        } finally { setIsLoading(false); }
    };

    const sendMessage = async () => {
        if (!input.trim() || isLoading || !sessionId) return;
        const userMessage = input.trim();
        setInput('');
        sendQuickMessage(userMessage);
    };

    // Hide everything if the bot is disabled by admin
    if (botSettings.is_active === false) return null;

    return (
        <>
            <style>{`
                @keyframes typing { 0%, 60%, 100% { transform: translateY(0); opacity: 0.4; } 30% { transform: translateY(-6px); opacity: 1; } }
                @keyframes slideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
                @keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); } 50% { box-shadow: 0 0 0 12px rgba(16, 185, 129, 0); } }
                
                #ai-messages::-webkit-scrollbar { width: 6px; }
                #ai-messages::-webkit-scrollbar-track { background: transparent; }
                #ai-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
                #ai-messages::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
                .product-list-container::-webkit-scrollbar { display: none; }
                .chat-container { overscroll-behavior: contain; }
                .starter-btn { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
                .starter-btn:hover { transform: translateY(-2px); background: rgba(255,255,255,0.15) !important; border-color: rgba(255,255,255,0.3) !important; }
                .starter-btn:active { transform: translateY(0); scale: 0.98; }
            `}</style>

            {isOpen && (
                <div 
                    id="ai-chatbot-window"
                    data-lenis-prevent
                    onWheel={e => e.stopPropagation()}
                    onTouchMove={e => e.stopPropagation()}
                    style={{
                        position: 'fixed', bottom: '130px', left: '20px', zIndex: 9999, width: 'calc(100% - 40px)', maxWidth: '400px', height: 'min(580px, 70vh)',
                        background: 'linear-gradient(160deg, #0f172a 0%, #1e293b 100%)', borderRadius: '24px', border: '1px solid rgba(255,255,255,0.1)',
                        boxShadow: '0 25px 60px rgba(0,0,0,0.5)', display: 'flex', flexDirection: 'column', overflow: 'hidden',
                        animation: 'slideUp 0.3s ease', backdropFilter: 'blur(20px)', overscrollBehavior: 'contain',
                    }}
                >

                    <div style={{ padding: '16px 20px', background: aiAgent.gradient, display: 'flex', alignItems: 'center', gap: '12px', flexShrink: 0, borderBottom: '1px solid rgba(0,0,0,0.05)' }}>
                        <div style={{ width: '44px', height: '44px', borderRadius: '50%', background: `url(${aiAgent.headerAvatar}) center/cover no-repeat, rgba(0,0,0,0.05)`, border: '1px solid rgba(0,0,0,0.1)' }} />
                        <div style={{ flex: 1 }}>
                            <div style={{ fontWeight: '700', color: '#1e293b', fontSize: '15px' }}>{aiAgent.name}</div>
                            <div style={{ color: '#64748b', fontSize: '12px' }}>{aiAgent.role} · Vion</div>
                        </div>
                        <button onClick={() => setIsOpen(false)} style={{ background: '#fee2e2', border: 'none', color: '#ef4444', width: '32px', height: '32px', borderRadius: '50%', cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '20px', fontWeight: 'bold', transition: 'all 0.2s' }} onMouseEnter={e => e.currentTarget.style.background = '#fecaca'} onMouseLeave={e => e.currentTarget.style.background = '#fee2e2'}>×</button>
                    </div>

                    {!sessionId ? (
                        <div style={{ flex: 1, overflowY: 'auto', overscrollBehavior: 'contain' }}>
                            <LeadForm onComplete={startChat} />
                        </div>
                    ) : (
                        <>
                            <div id="ai-messages" style={{ flex: 1, overflowY: 'auto', padding: '20px', display: 'flex', flexDirection: 'column', overscrollBehavior: 'contain' }}>
                                {messages.map((msg, idx) => (
                                    <div key={idx} ref={idx === messages.length - 1 ? lastMessageRef : null}>
                                        <MessageBubble msg={msg} allMessages={messages} waNumber={botSettings.whatsapp_number} />
                                    </div>
                                ))}

                                {/* Starter Buttons */}
                                {!isLoading && (messages.length === 1 || showActionsAlways) && botSettings.starter_buttons?.length > 0 && (
                                    <div style={{ 
                                        display: 'flex', flexWrap: 'wrap', gap: '8px', marginTop: '12px', paddingLeft: '40px', 
                                        animation: 'slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both' 
                                    }}>
                                        {botSettings.starter_buttons.map((btn, i) => (
                                            <button
                                                key={i}
                                                className="starter-btn"
                                                onClick={() => handleStarterClick(btn)}
                                                style={{
                                                    background: 'rgba(255,255,255,0.08)',
                                                    border: '1px solid rgba(255,255,255,0.15)',
                                                    borderRadius: '16px',
                                                    padding: '8px 14px',
                                                    color: '#fff',
                                                    fontSize: '12.5px',
                                                    cursor: 'pointer',
                                                    fontWeight: '500',
                                                    outline: 'none',
                                                    boxShadow: '0 4px 12px rgba(0,0,0,0.1)'
                                                }}
                                            >
                                                {btn.label}
                                            </button>
                                        ))}
                                    </div>
                                )}

                                {isLoading && (
                                    <div style={{ display: 'flex', gap: '8px', alignItems: 'flex-end', marginBottom: '16px' }}>
                                        <div style={{
                                            width: '32px', height: '32px', borderRadius: '50%', background: `url(${aiAgent.headerAvatar}) center/cover no-repeat, #f1f5f9`,
                                            flexShrink: 0, border: '1px solid rgba(0,0,0,0.05)',
                                        }} />
                                        <div style={{ 
                                            padding: '12px 16px', borderRadius: '18px 18px 18px 4px',
                                            background: 'rgba(30, 41, 59, 0.8)', backdropFilter: 'blur(10px)',
                                            border: '1px solid rgba(255,255,255,0.08)',
                                            display: 'flex', gap: '4px', alignItems: 'center'
                                        }}>
                                            {[0, 1, 2].map(i => (
                                                <div key={i} style={{ width: '6px', height: '6px', borderRadius: '50%', background: '#94a3b8', animation: 'typing 1s infinite', animationDelay: `${i * 0.2}s` }} />
                                            ))}
                                        </div>
                                    </div>
                                )}
                                <div ref={messagesEndRef} />
                            </div>

                            <div style={{ padding: '16px', background: 'rgba(15, 23, 42, 0.5)', borderTop: '1px solid rgba(255,255,255,0.05)', flexShrink: 0 }}>
                                <div style={{ position: 'relative', display: 'flex', alignItems: 'flex-end', gap: '8px' }}>
                                    <button
                                        onClick={() => setShowActionsAlways(!showActionsAlways)}
                                        title="Quick Actions"
                                        style={{
                                            width: '40px', height: '40px', borderRadius: '14px', border: '1px solid rgba(255,255,255,0.1)',
                                            background: showActionsAlways ? aiAgent.actionGradient : 'rgba(255,255,255,0.05)',
                                            color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center',
                                            cursor: 'pointer', transition: 'all 0.2s', marginBottom: '1px', flexShrink: 0
                                        }}
                                    >
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                                            <path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/>
                                        </svg>
                                    </button>

                                    <div style={{ position: 'relative', flex: 1 }}>
                                        <textarea
                                            ref={inputRef}
                                            value={input}
                                            onChange={(e) => setInput(e.target.value)}
                                            onKeyDown={(e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); } }}
                                            placeholder="Tanya VION..."
                                            style={{
                                                width: '100%', padding: '14px 45px 14px 16px', borderRadius: '18px', background: 'rgba(255,255,255,0.05)',
                                                border: '1px solid rgba(255,255,255,0.1)', color: '#fff', fontSize: '14px', outline: 'none',
                                                resize: 'none', height: '48px', lineHeight: '20px', transition: 'all 0.2s', display: 'block'
                                            }}
                                        />
                                        <button
                                            onClick={sendMessage}
                                            disabled={isLoading || !input.trim()}
                                            style={{
                                                position: 'absolute', right: '6px', bottom: '6px', width: '36px', height: '36px', borderRadius: '12px',
                                                background: input.trim() ? aiAgent.actionGradient : 'rgba(0,0,0,0.05)', border: 'none', color: '#fff',
                                                display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', transition: 'all 0.2s'
                                            }}
                                        >
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </>
                    )}
                </div>
            )}

            <button
                onClick={() => setIsOpen(!isOpen)}
                style={{
                    position: 'fixed', bottom: '24px', left: '24px', width: '130px', height: '130px',
                    background: 'transparent', border: 'none', cursor: 'pointer', zIndex: 9999,
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    transition: 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)',
                    transform: isOpen ? 'scale(0.8) rotate(5deg)' : 'scale(1)',
                }}
            >
                <div style={{ width: '100%', height: '100%', background: `url(${aiAgent.avatar}) center/contain no-repeat`, filter: 'drop-shadow(0 8px 16px rgba(0,0,0,0.2))' }} />
            </button>
        </>
    );
}
