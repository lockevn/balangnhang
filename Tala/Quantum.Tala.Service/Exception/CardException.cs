using System;

namespace Quantum.Tala.Service.Exception
{
    public class CardException : BusinessException
    {
        public CardException(string message) : base(message)
        {
            this.Source = "NOT_VALID";
        }
        
    }
}
