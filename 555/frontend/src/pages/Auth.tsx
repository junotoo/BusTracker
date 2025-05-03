import { useState } from "react";
import "./Auth.css"; // Import the CSS file for styling

interface AuthProps {
  onLogin: (email: string, password: string) => void;
  onSignUp: (email: string, password: string) => void;
}

const Auth = ({ onLogin, onSignUp }: AuthProps) => {
  const [isSignUp, setIsSignUp] = useState(false); // Toggle between Sign In and Sign Up
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (isSignUp) {
      if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
      }
      onSignUp(email, password);
    } else {
      onLogin(email, password);
    }
  };

  return (
    <div className="auth-container">
      <div className="auth-card">
        <h1 className="auth-title">{isSignUp ? "Create an Account" : "Welcome Back"}</h1>
        <p className="auth-subtitle">
          {isSignUp ? "Sign up to get started" : "Please log in to continue"}
        </p>
        <form onSubmit={handleSubmit} className="auth-form">
          <div className="auth-input-group">
            <label>Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              placeholder="Enter your email"
              className="auth-input"
            />
          </div>
          <div className="auth-input-group">
            <label>Password</label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              placeholder="Enter your password"
              className="auth-input"
            />
          </div>
          {isSignUp && (
            <div className="auth-input-group">
              <label>Confirm Password</label>
              <input
                type="password"
                value={confirmPassword}
                onChange={(e) => setConfirmPassword(e.target.value)}
                required
                placeholder="Confirm your password"
                className="auth-input"
              />
            </div>
          )}
          <button type="submit" className="auth-button">
            {isSignUp ? "Sign Up" : "Sign In"}
          </button>
        </form>
        <p className="auth-toggle">
          {isSignUp ? "Already have an account?" : "Don't have an account?"}{" "}
          <button
            type="button"
            onClick={() => setIsSignUp(!isSignUp)}
            className="auth-toggle-button"
          >
            {isSignUp ? "Sign In" : "Sign Up"}
          </button>
        </p>
      </div>
    </div>
  );
};

export default Auth;