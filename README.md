# E-Voting System for School Organization (OSIS)

> A modern and secure OSIS chairman election system built with PHP. Provides a responsive interface, real-time voting statistics, and a secure authentication system for school OSIS elections.

<div align="center">

![PHP Version](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

![Version](https://img.shields.io/badge/Version-1.0.0-blue?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Active-success?style=for-the-badge)
![Security](https://img.shields.io/badge/Security-Secure-brightgreen?style=for-the-badge)

</div>

## 🌐 Live Demo

You can see the live demo of this application at: [https://voting-pilketos.rf.gd/](https://voting-pilketos.rf.gd/)

## 📸 Preview

### Landing Page
![Landing Page](https://github.com/ardiajaa/E-Voting/blob/main/preview1.png)

### Admin Dashboard
![Admin Dashboard](https://github.com/ardiajaa/E-Voting/blob/main/preview2.png)

### User Dashboard
![User Dashboard](https://github.com/ardiajaa/E-Voting/blob/main/preview3.png)

## 🌟 Key Features

### 1. Authentication System
- **Multi-role Access**
  - Admin dashboard with full control
  - Student voting interface
  - Secure login system
- **Security Features**
  - Password encryption using PHP's password_hash()
  - Login history tracking
  - IP and device detection
  - Session management
  - CSRF protection

### 2. Voting Management
- **Real-time Voting**
  - Live vote counting
  - Progress tracking
  - Vote verification
  - Election period settings
- **Candidate Management**
  - Add/Edit/Delete candidates
  - Upload candidate photos
  - Manage candidate profiles
  - Track candidate statistics

### 3. Dashboard Features
- **Admin Dashboard**
  - Real-time voting statistics
  - Interactive charts and graphs
  - Candidate performance tracking
  - User management
  - System settings
- **Student Dashboard**
  - Clean and intuitive interface
  - Easy voting process
  - Vote confirmation
  - Profile management

### 4. School Information
- **Customizable Settings**
  - School name and logo
  - Academic year
  - OSIS vision and mission
  - Background customization
  - Election period settings

### 5. Responsive Design
- **Mobile-First Approach**
  - Fully responsive layout
  - Touch-friendly interface
  - Cross-browser compatibility
  - Modern UI/UX design

## 🛠️ Technical Stack

### Backend
- PHP 7.4+
- MySQL Database
- PDO for database operations
- Session management
- File handling

### Frontend
- HTML5/CSS3
- JavaScript
- Tailwind CSS
- Font Awesome Icons
- Chart.js for statistics
- AOS (Animate On Scroll)

### Security
- Password hashing
- SQL injection prevention
- XSS protection
- CSRF protection
- Input validation
- Session security

## 📋 Installation

1. **Clone the Repository**
```bash
git clone https://github.com/ardiajaa/E-Voting.git
cd E-Voting
```

2. **Database Setup**
```bash
# Import database structure
mysql -u username -p database_name < voting_osis.sql
```

3. **Configuration**
- Open `config/database.php`
- Update database credentials:
```php
$host = 'localhost';
$dbname = 'voting_osis';
$username = 'root';
$password = '';
```

4. **Web Server Setup**
- Point your web server to the project directory
- Ensure PHP and MySQL are installed
- Set proper permissions for uploads directory

5. **Initial Setup**
- Access admin panel
- Configure school settings
- Add candidates
- Set voting period

## 🔒 Security Features

### Authentication
- Secure password hashing
- Session management
- Login attempt tracking
- Device and IP logging

### Data Protection
- SQL injection prevention
- XSS protection
- CSRF tokens
- Input sanitization
- File upload validation

### Access Control
- Role-based access
- Session timeout
- Secure redirects
- Error handling

## 📱 Mobile Optimization

### Responsive Design
- Mobile-first approach
- Touch-friendly interface
- Adaptive layouts
- Optimized images

### Performance
- Lazy loading
- Image optimization
- Caching
- Minified assets

## 🎨 UI/UX Features

### Modern Interface
- Clean and intuitive design
- Smooth animations
- Interactive elements
- Progress indicators

### User Experience
- Easy navigation
- Clear instructions
- Responsive feedback
- Error handling

## 📊 Statistics and Reporting

### Real-time Analytics
- Vote counting
- Progress tracking
- Candidate statistics
- User participation

### Data Visualization
- Interactive charts
- Progress bars
- Status indicators
- Performance metrics

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👤 Contact

Ardi Aja - [@ardiajaa](https://github.com/ardiajaa)

Project Link: [https://github.com/ardiajaa/E-Voting](https://github.com/ardiajaa/E-Voting)

## 🙏 Acknowledgments

- [Tailwind CSS](https://tailwindcss.com) - For the amazing utility-first CSS framework
- [Font Awesome](https://fontawesome.com) - For the beautiful icons
- [Chart.js](https://www.chartjs.org) - For the interactive charts
- [AOS](https://michalsnik.github.io/aos/) - For the scroll animations
