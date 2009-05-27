using System;

namespace TalaAPI.Exception
{
    public class CardException : BusinessException
    {
        public CardException(string message) : base(message)
        {
            this.Source = "NOT_VALID";
        }
        
    }
}
